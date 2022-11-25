<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'enabled',
        'photo',
        'poste',
        'last_login_at'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['photo_full_path', 'is_admin', 'is_gestionnaire'];

    public function getPhotoFullPathAttribute()
    {
        return request()->getSchemeAndHttpHost() . '/uploads/user/photos/' . $this->photo;
    }

    /**
     * Get all of the intervenants for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function intervenants(): HasMany
    {
        return $this->hasMany(Intervenant::class);
    }

    /**
     * The applications that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'intervenants');
    }

    /**
     * The categorieReclamations that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categorieReclamations(): BelongsToMany
    {
        return $this->belongsToMany(CategorieReclamation::class, 'gestionnaires');
    }

    /**
     * Get all of the gestionnaires for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gestionnaires(): HasMany
    {
        return $this->hasMany(Gestionnaire::class);
    }

    public function getIsAdminAttribute()
    {
        return $this->type == 'Administrateur';
    }

    public function getIsGestionnaireAttribute()
    {
        return $this->type == 'Administrateur' || $this->type == 'Gestionnaire';
    }

    /**
     * Get all of the reclamations for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class);
    }
}
