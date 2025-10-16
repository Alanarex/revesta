<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address_id',
        'civil_status',
        'family_status',
        'cookies_accepted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public static function getFilterableAttributes(): array
    {
        return [
            'first_name' => 'Prénom',
            'last_name' => 'Nom',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'civil_status' => 'Situation civile',
            'family_status' => 'Situation familiale',
        ];
    }

    public static function getFilterableAttributeTypes(): array
    {
        return [
            'first_name' => 'text',
            'last_name' => 'text',
            'email' => 'text',
            'phone' => 'text',
            'civil_status' => 'text',
            'family_status' => 'text',
        ];
    }

}
