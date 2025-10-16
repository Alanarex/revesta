<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'postal_code',
        'street',
        'number',
        'complement',
        'city',
        'departement',
    ];

    public static function getFilterableAttributes(): array
    {
        return [
            'postal_code' => 'Code postal',
            'street' => 'Rue',
            'number' => 'Numéro',
            'complement' => 'Complément',
            'city' => 'Ville',
            'departement' => 'Département',
        ];
    }

    public static function getFilterableAttributeTypes(): array
    {
        return [
            'postal_code' => 'text',
            'street' => 'text',
            'number' => 'text',
            'complement' => 'text',
            'city' => 'text',
            'departement' => 'text',
        ];
    }

}