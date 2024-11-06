<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    public static function create(array $attributes = [])
    {
        $user = static::query()->create($attributes);
        return $user;
    }
    
    /*public function userable()
    {
        return $this->morphTo();
    }*/

    public function hasCompletedRegistration()
    {
        return $this->enseignants()->exists();
    }

    public function enseignants() {
        return $this->hasOne(Enseignant::class, 'user_id', 'id');
    }

    public function apprenants() {
        return $this->hasOne(Apprenant::class, 'user_id', 'id');
    }
    
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function isEnseignant()
    {
        return $this->role === 'enseignant';
    }
    
    public function getRedirectRoute(): string
    {
        return match((int)$this->role) {
            'admin' => 'admin.dashboard',
            'enseignant' => 'enseignant.dashboard',
            'apprenant' => 'apprenant.dashboard',
            // ...
        };
    }
}
