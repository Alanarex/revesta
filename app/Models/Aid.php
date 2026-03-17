<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aid extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'provider',
        'description',
        'type',
    ];

    public function simulations()
    {
        return $this->belongsToMany(Simulation::class, 'aid_simulation')
            ->using(AidSimulation::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    public static function findOrCreateByName(string $name, array $attributes = []): self
    {
        $aid = static::query()->firstOrNew(['name' => $name]);

        $updatableAttributes = array_filter(
            $attributes,
            static fn (mixed $value): bool => $value !== null && $value !== ''
        );

        if ($updatableAttributes !== []) {
            $aid->fill($updatableAttributes);
        }

        if (! $aid->exists || $aid->isDirty()) {
            $aid->save();
        }

        return $aid;
    }
}
