<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Newsletter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'ip_address',
        'subscribed_at',
        'verified_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Check if newsletter is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
