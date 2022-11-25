<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Priorite extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['icone_full_path'];

    public function getIconeFullPathAttribute()
    {
        return request()->getSchemeAndHttpHost() . '/uploads/priorite/icones/' . $this->icone;
    }

    /**
     * Get all of the reclamatons for the Priorite
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class);
    }
}
