<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenovationWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
    ];

    public function simulations()
    {
        return $this->belongsToMany(Simulation::class, 'renovation_work_simulation')
            ->withTimestamps();
    }
}
