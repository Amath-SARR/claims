<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profil extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * The applications that belong to the Profil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_profils');
    }

    /**
     * Get all of the applicationProfils for the Profil
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applicationProfils(): HasMany
    {
        return $this->hasMany(ApplicationProfil::class);
    }
}
