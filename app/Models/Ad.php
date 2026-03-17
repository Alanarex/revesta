<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'url',
        'site',
        'titre',
        'prix',
        'localisation',
        'ville',
        'code_postal',
        'surface',
        'pieces',
        'description',
        'housing_type_id',
        'dpe_class_id',
        'etage',
        'type_travaux',
        'date_extraction',
        'housing_id',
    ];

    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
            'surface' => 'decimal:2',
            'pieces' => 'decimal:2',
            'date_extraction' => 'datetime',
        ];
    }

    public function housing()
    {
        return $this->belongsTo(Housing::class);
    }

    public function images()
    {
        return $this->hasMany(AdImage::class);
    }
}
