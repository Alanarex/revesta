<?php

namespace App\Repositories;

use App\Models\Ad;
use Illuminate\Support\Arr;

class AdRepository
{
    public function upsertFromPayload(array $payload): Ad
    {
        $url = trim((string) Arr::get($payload, 'url', Arr::get($payload, 'url_annonce', '')));
        $site = Arr::get($payload, 'site');

        $attributes = [
            'url' => $url,
            'site' => $site,
            'titre' => Arr::get($payload, 'titre'),
            'prix' => Arr::get($payload, 'prix'),
            'localisation' => Arr::get($payload, 'localisation'),
            'ville' => Arr::get($payload, 'ville'),
            'code_postal' => Arr::get($payload, 'code_postal'),
            'surface' => Arr::get($payload, 'surface'),
            'pieces' => Arr::get($payload, 'pieces'),
            'description' => Arr::get($payload, 'description'),
            'housing_type_id' => $this->resolveHousingTypeId(Arr::get($payload, 'type_logement')),
            'dpe_class_id' => $this->resolveDpeClassId(Arr::get($payload, 'dpe')),
            'etage' => Arr::get($payload, 'etage'),
            'type_travaux' => Arr::get($payload, 'type_travaux'),
            'date_extraction' => Arr::get($payload, 'date_extraction'),
        ];

        $query = Ad::query()->withTrashed()->where('url', $url);
        if (! empty($site)) {
            $query->where('site', $site);
        }

        $ad = $query->first();

        if ($ad) {
            if (method_exists($ad, 'trashed') && $ad->trashed()) {
                $ad->restore();
            }

            $ad->fill(array_filter($attributes, fn ($value) => $value !== null));
            $ad->save();
        } else {
            $ad = Ad::query()->create($attributes);
        }

        $images = [];

        foreach ((array) Arr::get($payload, 'images', []) as $index => $imageInput) {
            if (is_string($imageInput) && trim($imageInput) !== '') {
                $images[] = ['url' => trim($imageInput), 'order' => $index];
                continue;
            }

            if (is_array($imageInput)) {
                $url = trim((string) ($imageInput['url'] ?? ''));
                if ($url !== '') {
                    $order = isset($imageInput['order']) && is_numeric($imageInput['order'])
                        ? (int) $imageInput['order']
                        : $index;
                    $images[] = ['url' => $url, 'order' => $order];
                }
            }
        }

        foreach ($images as $image) {
            $ad->images()->updateOrCreate(
                ['url' => $image['url']],
                ['order' => $image['order']]
            );
        }

        return $ad;
    }

    private function resolveHousingTypeId(mixed $value): ?string
    {
        $input = mb_strtolower(trim((string) $value));
        if ($input === '' || $input === 'indifferent') {
            return null;
        }

        return $input;
    }

    private function resolveDpeClassId(mixed $value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $upper = mb_strtoupper($raw);
        $numericMap = (array) config('housing.dpe_numeric_map', []);
        return $numericMap[$raw] ?? $upper;
    }
}
