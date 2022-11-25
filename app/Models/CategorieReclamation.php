<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieReclamation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the application that owns the CategorieReclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get all of the gestionnaires for the CategorieReclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gestionnaires(): HasMany
    {
        return $this->hasMany(Gestionnaire::class);
    }

    /**
     * Get all of the reclammations for the CategorieReclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class);
    }
}
