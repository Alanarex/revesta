<?php

namespace App\Repositories;

use App\Models\Ad;
use App\Models\Aid;
use App\Models\RenovationWork;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SimulationRepository
{
    public function createSimulation(User $user, Ad $ad, array $payload): Simulation
    {
        return Simulation::query()->create([
            'user_id'             => $user->id,
            'ad_id'               => $ad->id,
            'date'                => Arr::get($payload, 'date', now()->toDateString()),
            'gain_energetique'    => Arr::get($payload, 'gain_energetique'),
            'aid_path_id'         => $this->resolveAidPathId(Arr::get($payload, 'parcours_aide')),
            'condition_depenses'  => Arr::get($payload, 'condition_depenses'),
            'montant_total_aides' => Arr::get($payload, 'montant_total_aides'),
            'pourcentage_bien'    => Arr::get($payload, 'pourcentage_bien'),
            'aides_details'       => Arr::get($payload, 'aides_details', []),
        ]);
    }

    public function syncWorks(Simulation $simulation, array $works): void
    {
        $workIds = [];

        foreach ($works as $work) {
            if (is_array($work)) {
                $work = Arr::get($work, 'type', Arr::get($work, 'label', ''));
            }

            $label = trim((string) $work);
            if ($label === '') {
                continue;
            }

            $model = RenovationWork::query()->firstOrCreate(
                ['code' => Str::slug($label, '_')],
                ['label' => $label]
            );

            $workIds[] = $model->id;
        }

        if (! empty($workIds)) {
            $simulation->works()->sync($workIds);
        }
    }

    public function syncAids(Simulation $simulation, array $aides): void
    {
        foreach ($aides as $aide) {
            $name = trim((string) Arr::get($aide, 'nom', ''));
            if ($name === '') {
                continue;
            }

            $aid = Aid::findOrCreateByName($name, [
                'provider'    => 'Extension REVESTA',
                'description' => Arr::get($aide, 'description', 'Aide synchronisée depuis la simulation publique.'),
                'type'        => Arr::get($aide, 'type', 'subvention'),
            ]);

            $simulation->aids()->syncWithoutDetaching([
                $aid->id => [
                    'amount'   => $this->normalizeAidAmount(Arr::get($aide, 'montant')),
                    'raw_name' => $name,
                    'details'  => [
                        'url'         => Arr::get($aide, 'url'),
                        'type'        => Arr::get($aide, 'type'),
                        'description' => Arr::get($aide, 'description'),
                    ],
                ],
            ]);
        }
    }

    private function resolveAidPathId(mixed $value): ?string
    {
        $input = mb_strtolower(trim((string) $value));
        if ($input === '') {
            return null;
        }

        $aliases = (array) config('aid.parcours_aide_aliases', []);
        return $aliases[$input] ?? null;
    }

    private function normalizeAidAmount(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }
}

