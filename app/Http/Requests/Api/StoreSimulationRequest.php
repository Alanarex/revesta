<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSimulationRequest extends FormRequest
{
    private array $originalPayload = [];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->originalPayload === []) {
            $this->originalPayload = $this->all();
        }

        $annonce = (array) $this->input('annonce', []);
        $utilisateur = (array) $this->input('utilisateur', []);
        $simulation = (array) $this->input('simulation', []);

        if (! isset($annonce['url']) && isset($annonce['url_annonce']) && is_string($annonce['url_annonce'])) {
            $annonce['url'] = $annonce['url_annonce'];
        }

        if (isset($annonce['images']) && is_array($annonce['images'])) {
            $normalizedImages = [];

            foreach ($annonce['images'] as $image) {
                if (is_string($image) && trim($image) !== '') {
                    $normalizedImages[] = trim($image);
                    continue;
                }

                if (is_array($image)) {
                    $url = $image['url'] ?? null;
                    if (is_string($url) && trim($url) !== '') {
                        $normalizedImages[] = trim($url);
                    }
                }
            }

            $annonce['images'] = $normalizedImages;
        }

        if (isset($utilisateur['email']) && is_string($utilisateur['email'])) {
            $utilisateur['email'] = mb_strtolower(trim($utilisateur['email']));
        }

        if (isset($simulation['travaux']) && is_array($simulation['travaux'])) {
            $normalizedWorks = [];

            foreach ($simulation['travaux'] as $work) {
                if (is_string($work) && trim($work) !== '') {
                    $normalizedWorks[] = trim($work);
                    continue;
                }

                if (is_array($work)) {
                    $label = $work['type'] ?? $work['label'] ?? null;
                    if (is_string($label) && trim($label) !== '') {
                        $normalizedWorks[] = trim($label);
                    }
                }
            }

            $simulation['travaux'] = $normalizedWorks;
        }

        if (isset($simulation['aides_details']) && is_array($simulation['aides_details'])) {
            $normalizedAides = [];

            foreach ($simulation['aides_details'] as $aide) {
                if (! is_array($aide)) {
                    continue;
                }

                $nom = isset($aide['nom']) ? trim((string) $aide['nom']) : '';
                $detail = isset($aide['detail'])
                    ? trim((string) $aide['detail'])
                    : trim((string) ($aide['description'] ?? ''));
                $type = isset($aide['type']) ? trim((string) $aide['type']) : '';

                $valeurRaw = $aide['valeur'] ?? ($aide['montant'] ?? null);
                $valeur = is_numeric($valeurRaw) ? (float) $valeurRaw : null;

                $normalizedAid = [
                    'nom' => $nom,
                    'detail' => $detail !== '' ? $detail : null,
                    'type' => $type !== '' ? $type : null,
                    'valeur' => $valeur,
                ];

                if (isset($aide['url']) && is_string($aide['url']) && trim($aide['url']) !== '') {
                    $normalizedAid['url'] = trim($aide['url']);
                }

                $normalizedAides[] = $normalizedAid;
            }

            $simulation['aides_details'] = $normalizedAides;
        }

        $this->merge([
            'annonce' => $annonce,
            'utilisateur' => $utilisateur,
            'simulation' => $simulation,
        ]);
    }

    public function originalPayload(): array
    {
        return $this->originalPayload;
    }

    public function rules(): array
    {
        return [
            'annonce' => ['required', 'array'],
            'annonce.url' => ['required', 'url:http,https', 'max:2048'],
            'annonce.site' => ['nullable', 'string', 'max:100'],
            'annonce.titre' => ['nullable', 'string', 'max:255'],
            'annonce.prix' => ['nullable', 'numeric', 'min:0'],
            'annonce.localisation' => ['nullable', 'string', 'max:255'],
            'annonce.ville' => ['nullable', 'string', 'max:150'],
            'annonce.code_postal' => ['nullable', 'string', 'max:20'],
            'annonce.surface' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'annonce.pieces' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'annonce.description' => ['nullable', 'string', 'max:10000'],
            'annonce.type_logement' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(array_values(array_map('strval', array_keys((array) config('housing.housing_types', []))))),
            ],
            'annonce.dpe' => [
                'nullable',
                'string',
                'max:5',
                Rule::in(array_values(array_map('strval', array_unique(array_merge(
                    array_keys((array) config('housing.dpe_classes', [])),
                    array_keys((array) config('housing.dpe_numeric_map', []))
                ))))),
            ],
            'annonce.etage' => ['nullable', 'string', 'max:100'],
            'annonce.type_travaux' => ['nullable', 'string', 'max:255'],
            'annonce.date_extraction' => ['nullable', 'date'],
            'annonce.images' => ['nullable', 'array', 'max:20'],
            'annonce.images.*' => ['required', 'url:http,https', 'max:2048'],

            'utilisateur' => ['required', 'array'],
            'utilisateur.email' => ['required', 'email:rfc', 'max:255'],
            'utilisateur.prenom' => ['nullable', 'string', 'max:150'],
            'utilisateur.nom' => ['nullable', 'string', 'max:150'],
            'utilisateur.telephone' => ['nullable', 'string', 'max:50'],
            'utilisateur.code_postal' => ['nullable', 'string', 'max:20'],
            'utilisateur.revenus' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'utilisateur.nombre_personnes' => ['nullable', 'integer', 'min:1', 'max:30'],
            'utilisateur.residence_principale' => ['nullable', 'boolean'],
            'utilisateur.budget_achat' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'utilisateur.surface_logement' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'utilisateur.budget_travaux' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'utilisateur.taxe_fonciere' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'utilisateur.condition_depenses' => ['nullable', 'boolean'],
            'utilisateur.notifications_aides' => ['nullable', 'boolean'],
            'utilisateur.notifications_prix' => ['nullable', 'boolean'],
            'utilisateur.accept_analytics' => ['nullable', 'boolean'],
            'utilisateur.statut' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(array_values(array_map('strval', array_unique(array_merge(
                    array_keys((array) config('users.simulation_statuses', [])),
                    array_keys((array) config('users.simulation_status_aliases', []))
                ))))),
            ],
            'utilisateur.dpe_actuel' => [
                'nullable',
                'string',
                'max:5',
                Rule::in(array_values(array_map('strval', array_unique(array_merge(
                    array_keys((array) config('housing.dpe_classes', [])),
                    array_keys((array) config('housing.dpe_numeric_map', []))
                ))))),
            ],
            'utilisateur.dpe_vise' => [
                'nullable',
                'string',
                'max:5',
                Rule::in(array_values(array_map('strval', array_unique(array_merge(
                    array_keys((array) config('housing.dpe_classes', [])),
                    array_keys((array) config('housing.dpe_numeric_map', []))
                ))))),
            ],
            'utilisateur.periode_construction' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(array_values(array_map('strval', array_unique(array_merge(
                    array_keys((array) config('housing.construction_periods', [])),
                    array_keys((array) config('housing.construction_period_aliases', []))
                ))))),
            ],
            'utilisateur.type_logement' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(array_values(array_map('strval', array_keys((array) config('housing.housing_types', []))))),
            ],
            'utilisateur.gain_energetique' => [
                'nullable',
                'integer',
                Rule::in(array_values(array_map('intval', array_keys((array) config('housing.energy_gain_targets', []))))),
            ],
            'utilisateur.parcours_aide' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(array_values(array_map('strval', array_unique(array_merge(
                    array_keys((array) config('aid.parcours_aide', [])),
                    array_keys((array) config('aid.parcours_aide_aliases', []))
                ))))),
            ],

            'simulation' => ['required', 'array'],
            'simulation.gain_energetique' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'simulation.parcours_aide' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(array_values(array_map('strval', array_unique(array_merge(
                    array_keys((array) config('aid.parcours_aide', [])),
                    array_keys((array) config('aid.parcours_aide_aliases', []))
                ))))),
            ],
            'simulation.condition_depenses' => ['nullable', 'boolean'],
            'simulation.montant_total_aides' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'simulation.pourcentage_bien' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'simulation.aides_details' => ['nullable', 'array', 'max:50'],
            'simulation.aides_details.*.nom' => ['required_with:simulation.aides_details', 'string', 'max:255'],
            'simulation.aides_details.*.detail' => ['nullable', 'string', 'max:1000'],
            'simulation.aides_details.*.valeur' => ['nullable', 'numeric', 'min:0'],
            'simulation.aides_details.*.montant' => ['nullable', 'numeric', 'min:0'],
            'simulation.aides_details.*.valeur' => ['nullable', 'numeric', 'min:0'],
            'simulation.aides_details.*.type' => ['nullable', 'string', 'max:100'],
            'simulation.aides_details.*.description' => ['nullable', 'string', 'max:1000'],
            'simulation.aides_details.*.detail' => ['nullable', 'string', 'max:1000'],
            'simulation.aides_details.*.url' => ['nullable', 'url:http,https', 'max:2048'],
            'simulation.travaux' => ['nullable', 'array', 'max:30'],
            'simulation.travaux.*' => ['required', 'string', 'max:150'],
            'simulation.date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'utilisateur.email.required' => "L'email utilisateur est obligatoire.",
            'utilisateur.email.email' => "L'email utilisateur est invalide.",
            'annonce.images.*.url' => 'Chaque image doit être une URL valide en http/https.',
            'simulation.aides_details.*.nom.required_with' => "Chaque aide doit contenir un nom.",
        ];
    }
}
