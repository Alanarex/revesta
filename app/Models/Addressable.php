<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Addressable extends Model
{
    protected $fillable = [
        'address_id',
        'addressable_type',
        'addressable_id',
        'type',
    ];

    protected $casts = [
        'type' => 'integer',
    ];

    /**
     * Get the associated address
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the owning addressable model
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
