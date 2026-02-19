<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Address Model
 *
 * @property int $id
 * @property string $label
 * @property string $street
 * @property string|null $number
 * @property string|null $complement
 * @property string $postal_code
 * @property string $city
 * @property string|null $departement
 * @property string|null $insee_code
 * @property float|null $lat
 * @property float|null $lng
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'postal_code',
        'street',
        'number',
        'complement',
        'city',
        'departement',
        'insee_code',
        'lat',
        'lng',
    ];

    public static function getFilterableAttributes(): array
    {
        return [
            'label' => 'Libellé',
            'postal_code' => 'Code postal',
            'street' => 'Rue',
            'number' => 'Numéro',
            'complement' => 'Complément',
            'city' => 'Ville',
            'departement' => 'Département',
            'insee_code' => 'Code INSEE',
        ];
    }

    public static function getFilterableAttributeTypes(): array
    {
        return [
            'label' => 'text',
            'postal_code' => 'text',
            'street' => 'text',
            'number' => 'text',
            'complement' => 'text',
            'city' => 'text',
            'departement' => 'text',
            'insee_code' => 'text',
        ];
    }

    /**
     * Polymorphic relation: Get all users associated with this address
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'addressable');
    }

    /**
     * Polymorphic relation: Get all housings associated with this address
     */
    public function housings()
    {
        return $this->morphedByMany(Housing::class, 'addressable');
    }

    /**
     * Get all models that have this address
     */
    public function addressables()
    {
        return $this->hasMany(Addressable::class);
    }
}
