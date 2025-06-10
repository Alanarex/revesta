<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aid extends Model
{
    use SoftDeletes, HasFactory;

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
}
