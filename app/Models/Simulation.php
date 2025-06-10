<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Simulation extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aids()
    {
        return $this->belongsToMany(Aid::class, 'aid_simulation')
            ->using(AidSimulation::class)
            ->withPivot('amount')
            ->withTimestamps();
    }
}
