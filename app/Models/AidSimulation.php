<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AidSimulation extends Pivot
{
    use HasFactory;
    protected $table = 'aid_simulation';

    protected $fillable = [
        'simulation_id',
        'aid_id',
        'amount',
    ];
}