<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gestionnaire extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the user that owns the Gestionnaire
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the categorieReclamation that owns the Gestionnaire
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categorieReclamation(): BelongsTo
    {
        return $this->belongsTo(CategorieReclamation::class);
    }
}
