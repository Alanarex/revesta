<?php

namespace App\Models;

use App\Mail\PasswordResetMail;
use App\Mail\EmailVerificationMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements OAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'bio',
        'email_verified_at',
        'role_id',
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

    /**
     * Polymorphic relationship: Get all addresses for this user
     */
    public function addresses()
    {
        return $this->morphToMany(Address::class, 'addressable')->withTimestamps();
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

    /**
     * Accessor: full_name
     * Combines first_name and last_name, falling back to `name` or 'User'.
     */
    public function getFullNameAttribute(): string
    {
        $first = trim((string) $this->first_name);
        $last = trim((string) $this->last_name);
        $combined = trim($first.' '.$last);
        if ($combined !== '') {
            return $combined;
        }

        return (string) ($this->name ?? 'User');
    }

    /**
     * Accessor: initials
     * Builds uppercase initials from first_name and last_name, falling back to first char of name or 'U'.
     */
    public function getInitialsAttribute(): string
    {
        $firstInitial = $this->first_name ? mb_substr($this->first_name, 0, 1) : ($this->name ? mb_substr($this->name, 0, 1) : 'U');
        $lastInitial = $this->last_name ? mb_substr($this->last_name, 0, 1) : '';

        return mb_strtoupper($firstInitial.$lastInitial);
    }

    /**
     * Convenience helper to check whether the user is an administrator.
     * Relies on the `role` relation and the `roles.name` value.
     */
    public function isAdmin(): bool
    {
        return $this->role && isset($this->role->name) && strcasecmp($this->role->name, 'admin') === 0;
    }

    /**
     * Get the blog bookmarks for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogBookmarks()
    {
        return $this->hasMany(BlogBookmark::class);
    }

    /**
     * Get the blogs authored by the user.
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    /**
     * Get the comments made by the user on blogs.
     */
    public function blogComments()
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Get the likes made by the user on blogs and comments.
     */
    public function blogLikes()
    {
        return $this->hasMany(BlogLike::class);
    }

    /**
     * Get all notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the simulations belonging to the user.
     */
    public function simulations()
    {
        return $this->hasMany(Simulation::class);
    }

    /**
     * Send the password reset notification to the user.
     * Overrides Laravel's default password reset notification with a custom mailable.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(config('auth.passwords.users.expire', 15)),
            ['token' => $token]
        );

        Mail::send(new PasswordResetMail(
            userName: $this->first_name ?: $this->email,
            resetUrl: $resetUrl,
            recipientEmail: $this->email,
            expirationMinutes: config('auth.passwords.users.expire', 15)
        ));
    }

    /**
     * Send the email verification notification to the user.
     * Overrides Laravel's default email verification notification with a custom mailable.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        Mail::send(new EmailVerificationMail($this));
    }
}
