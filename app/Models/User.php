<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasTenants, CanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function organisation(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class);
    }

    public function guests(): HasMany
    {

        return $this->hasMany(Guest::class);
    }


    // public function organisations(): BelongsToMany
    // {
    //     return $this->belongsToMany(Organisation::class);
    // }

    public function getTenants(Panel $panel): Collection
    {
        return $this->organisation;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->organisation()->whereKey($tenant)->exists();
    }

    function canAccessPanel(Panel $panel): bool
    {

        // Check if the user is a super admin and can access the super admin panel
        if ($panel->getId() === 'super-admin' && $this->role === 'super-admin') {

            // dd($panel->getId());
            return true;
        }

        // Check if the user is an admin and can access the admin panel
        if ($panel->getId() === 'admin' && $this->role === 'admin') {

            // dd($panel->getId());
            return true;
        }

        return false;  // Return false if the user doesn't have access to the panel
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
}
