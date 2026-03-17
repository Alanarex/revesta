<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Simulation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'ad_id',
        'date',
        'gain_energetique',
        'aid_path_id',
        'condition_depenses',
        'montant_total_aides',
        'pourcentage_bien',
        'aides_details',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'gain_energetique' => 'decimal:2',
            'condition_depenses' => 'boolean',
            'montant_total_aides' => 'decimal:2',
            'pourcentage_bien' => 'decimal:2',
            'aides_details' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function works()
    {
        return $this->belongsToMany(RenovationWork::class, 'renovation_work_simulation')
            ->withTimestamps();
    }

    public function aids()
    {
        return $this->belongsToMany(Aid::class, 'aid_simulation')
            ->using(AidSimulation::class)
            ->withPivot('amount', 'raw_name', 'details')
            ->withTimestamps();
    }
}
