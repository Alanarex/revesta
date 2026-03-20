<?php

namespace Tests\Feature\Api;

use App\Mail\SimulationReportMail;
use App\Models\Aid;
use App\Models\RenovationWork;
use App\Models\Simulation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SimulationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_simulation_payload_with_mixed_extension_formats(): void
    {
        Mail::fake();
        Storage::fake('local');

        $payload = [
            'annonce' => [
                'site' => 'leboncoin',
                'titre' => 'Maison 4 pièces 92 m²',
                'prix' => 245000,
                'localisation' => 'Centre-ville',
                'ville' => 'Nantes',
                'code_postal' => '44000',
                'surface' => 92,
                'pieces' => 4,
                'description' => 'Maison avec jardin...',
                'type_logement' => 'maison',
                'dpe' => 'E',
                'etage' => 'RDC',
                'type_travaux' => 'rénovation énergétique',
                'url_annonce' => 'https://example.com/annonce/123',
                'date_extraction' => '2026-03-16T10:30:00Z',
                'images' => [
                    'https://cdn.example.com/image1.jpg',
                    ['url' => 'https://cdn.example.com/image2.jpg', 'order' => 1],
                ],
            ],
            'utilisateur' => [
                'email' => 'user@example.com',
                'prenom' => 'Jean',
                'nom' => 'Dupont',
                'telephone' => '+33612345678',
                'statut' => 'proprietaire',
                'revenus' => 42000,
                'nombre_personnes' => 3,
                'residence_principale' => true,
                'dpe_actuel' => 'F',
                'dpe_vise' => 'C',
                'budget_achat' => 260000,
                'surface_logement' => 92,
                'periode_construction' => '1949-1974',
                'budget_travaux' => 35000,
                'taxe_fonciere' => 1200,
            ],
            'simulation' => [
                'gain_energetique' => 38.5,
                'parcours_aide' => 'maprimerenov',
                'condition_depenses' => true,
                'travaux' => [
                    ['type' => 'isolation', 'cout' => 12000],
                ],
                'montant_total_aides' => 18500,
                'pourcentage_bien' => 7.55,
                'aides_details' => [
                    ['nom' => "MaPrimeRénov'", 'montant' => 9000],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/simulations', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['simulation_id', 'user_id', 'user_created']);

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'user_status_id' => 'proprietaire_occupant',
            'dpe_actuel_id' => 'F',
            'dpe_vise_id' => 'C',
            'construction_period_id' => 'plus_15_ans',
        ]);

        $this->assertDatabaseHas('ads', [
            'url' => 'https://example.com/annonce/123',
            'site' => 'leboncoin',
            'type_travaux' => 'rénovation énergétique',
            'housing_type_id' => 'maison',
            'dpe_class_id' => 'E',
        ]);

        $simulation = Simulation::query()->findOrFail($response->json('simulation_id'));

        $this->assertSame('maprimerenov', $simulation->aid_path_id);

        $archivedFiles = Storage::disk('local')->allFiles('simulation-api-payloads');
        $this->assertCount(1, $archivedFiles);
        $this->assertSame($payload, json_decode(Storage::disk('local')->get($archivedFiles[0]), true, 512, JSON_THROW_ON_ERROR));

        $this->assertDatabaseCount('ad_images', 2);

        $work = RenovationWork::query()->where('code', 'isolation')->first();
        $this->assertNotNull($work);
        $this->assertDatabaseHas('renovation_work_simulation', [
            'simulation_id' => $simulation->id,
            'renovation_work_id' => $work->id,
        ]);

        $aid = Aid::query()->where('name', "MaPrimeRénov'")->first();
        $this->assertNotNull($aid);
        $this->assertDatabaseHas('aid_simulation', [
            'simulation_id' => $simulation->id,
            'aid_id' => $aid->id,
            'raw_name' => "MaPrimeRénov'",
        ]);

        Mail::assertQueued(SimulationReportMail::class);
    }

    public function test_it_rejects_unknown_parcours_aide(): void
    {
        $payload = [
            'annonce' => [
                'url' => 'https://example.com/annonce/123',
            ],
            'utilisateur' => [
                'email' => 'user@example.com',
            ],
            'simulation' => [
                'parcours_aide' => 'unknown-path',
            ],
        ];

        $response = $this->postJson('/api/v1/simulations', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['simulation.parcours_aide']);
    }

    public function test_it_still_saves_and_returns_success_when_mail_queue_fails(): void
    {
        Storage::fake('local');
        Log::spy();

        Mail::shouldReceive('to')
            ->once()
            ->andReturn(new class
            {
                public function queue($mailable): void
                {
                    throw new \RuntimeException('Mail transport failed.');
                }
            });

        $payload = [
            'annonce' => [
                'url' => 'https://example.com/annonce/with-mail-error',
            ],
            'utilisateur' => [
                'email' => 'mail-error@example.com',
            ],
            'simulation' => [
                'parcours_aide' => 'maprimerenov',
            ],
        ];

        $response = $this->postJson('/api/v1/simulations', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'email' => 'mail-error@example.com',
        ]);

        $this->assertDatabaseHas('ads', [
            'url' => 'https://example.com/annonce/with-mail-error',
        ]);

        $this->assertDatabaseHas('simulations', [
            'id' => $response->json('simulation_id'),
        ]);

        $archivedFiles = Storage::disk('local')->allFiles('simulation-api-payloads');
        $this->assertCount(1, $archivedFiles);

        Log::shouldHaveReceived('warning')
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'Failed to queue simulation report mail.'
                    && array_key_exists('simulation_id', $context)
                    && array_key_exists('user_id', $context);
            })
            ->atLeast()->once();
    }

    public function test_it_defaults_missing_aid_amount_to_zero_and_keeps_other_payload_data(): void
    {
        Mail::fake();
        Storage::fake('local');

        $payload = [
            'annonce' => [
                'site' => 'leboncoin',
                'titre' => 'Maison avec aides sans montant',
                'url_annonce' => 'https://example.com/annonce/no-amount',
                'type_logement' => 'maison',
                'dpe' => 'E',
            ],
            'utilisateur' => [
                'email' => 'zero-amount@example.com',
                'prenom' => 'Jeanne',
                'nom' => 'Martin',
                'statut' => 'proprietaire',
                'dpe_actuel' => 'F',
                'dpe_vise' => 'C',
                'periode_construction' => '1949-1974',
            ],
            'simulation' => [
                'parcours_aide' => 'maprimerenov',
                'travaux' => [
                    ['type' => 'ventilation'],
                ],
                'aides_details' => [
                    ['nom' => 'Éco-prêt à taux zéro (éco PTZ)'],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/simulations', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $simulation = Simulation::query()->findOrFail($response->json('simulation_id'));
        $aid = Aid::query()->where('name', 'Éco-prêt à taux zéro (éco PTZ)')->first();

        $this->assertNotNull($aid);

        $this->assertDatabaseHas('aid_simulation', [
            'simulation_id' => $simulation->id,
            'aid_id' => $aid->id,
            'amount' => 0,
            'raw_name' => 'Éco-prêt à taux zéro (éco PTZ)',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'zero-amount@example.com',
            'user_status_id' => 'proprietaire_occupant',
            'dpe_actuel_id' => 'F',
            'dpe_vise_id' => 'C',
            'construction_period_id' => 'plus_15_ans',
        ]);

        $this->assertDatabaseHas('ads', [
            'url' => 'https://example.com/annonce/no-amount',
            'housing_type_id' => 'maison',
            'dpe_class_id' => 'E',
        ]);

        $this->assertDatabaseHas('renovation_works', [
            'code' => 'ventilation',
        ]);
    }

    public function test_it_accepts_new_aides_structure_with_detail_type_valeur(): void
    {
        Mail::fake();
        Storage::fake('local');

        $payload = [
            'annonce' => [
                'url' => 'https://example.com/annonce/new-aides-structure',
            ],
            'utilisateur' => [
                'email' => 'new-aides-structure@example.com',
            ],
            'simulation' => [
                'parcours_aide' => 'maprimerenov',
                'aides_details' => [
                    [
                        'nom' => "MaPrimeRénov' parcours accompagné",
                        'detail' => 'Subvention directe • 18 000 €',
                        'type' => 'subvention',
                        'valeur' => 18000,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/simulations', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $simulation = Simulation::query()->findOrFail($response->json('simulation_id'));
        $aid = Aid::query()->where('name', "MaPrimeRénov' parcours accompagné")->first();

        $this->assertNotNull($aid);

        $this->assertDatabaseHas('aid_simulation', [
            'simulation_id' => $simulation->id,
            'aid_id' => $aid->id,
            'amount' => 18000,
            'raw_name' => "MaPrimeRénov' parcours accompagné",
        ]);

        $this->assertEquals(
            [
                [
                    'nom' => "MaPrimeRénov' parcours accompagné",
                    'detail' => 'Subvention directe • 18 000 €',
                    'type' => 'subvention',
                    'valeur' => 18000,
                ],
            ],
            $simulation->aides_details
        );
    }

    public function test_it_normalizes_extension_scalar_types_before_validation(): void
    {
        Mail::fake();
        Storage::fake('local');

        $payload = [
            'annonce' => [
                'url' => 'https://example.com/annonce/type-normalization',
                'dpe' => 4,
                'etage' => 3,
            ],
            'utilisateur' => [
                'email' => 'normalized-types@example.com',
                'residence_principale' => 'oui',
                'dpe_actuel' => 6,
                'dpe_vise' => 2,
            ],
            'simulation' => [
                'condition_depenses' => true,
            ],
        ];

        $response = $this->postJson('/api/v1/simulations', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'email' => 'normalized-types@example.com',
            'residence_principale' => true,
            'dpe_actuel_id' => 'F',
            'dpe_vise_id' => 'B',
        ]);

        $this->assertDatabaseHas('ads', [
            'url' => 'https://example.com/annonce/type-normalization',
            'dpe_class_id' => 'D',
            'etage' => '3',
        ]);
    }
}
