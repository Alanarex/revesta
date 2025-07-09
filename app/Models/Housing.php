<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Housing extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'type',
        'surface',
        'construction_year',
        'energy_class',
        'adresse_id',
        'fiscal_income_id',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'adresse_id');
    }

    public function fiscalIncomeRange()
    {
        return $this->belongsTo(FiscalIncomeRange::class, 'fiscal_income_id');
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public static function getFilterableAttributes(): array
    {
        return [
            'type' => 'Type de logement',
            'surface' => 'Surface',
            'construction_year' => 'Année de construction',
            'energy_class' => 'Classe énergétique',
        ];
    }

    public static function getFilterableAttributeTypes(): array
    {
        return [
            'type' => 'text',
            'surface' => 'number',
            'construction_year' => 'number',
            'energy_class' => 'text',
        ];
    }
}
