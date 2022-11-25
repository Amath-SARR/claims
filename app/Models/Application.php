<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code', 'presentation', 'logo'];
    protected $appends = ['logo_full_path'];

    public function getLogoFullPathAttribute()
    {
        return request()->getSchemeAndHttpHost() . '/uploads/application/logos/' . $this->logo;
    }

    /**
     * Get all of the intervenants for the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function intervenants(): HasMany
    {
        return $this->hasMany(Intervenant::class);
    }

    /**
     * The users that belong to the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'intervenants');
    }

    /**
     * Get all of the applicationProfils for the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applicationProfils(): HasMany
    {
        return $this->hasMany(ApplicationProfil::class);
    }

    /**
     * Get all of the categorieReclamations for the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categorieReclamations(): HasMany
    {
        return $this->hasMany(CategorieReclamation::class);
    }

    /**
     * Get all of the reclamations for the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function reclamations(): HasManyThrough
    {
        return $this->hasManyThrough(Reclamation::class, CategorieReclamation::class);
    }
}
