<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reclamation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['number'];

    /**
     * Get all of the reclamationStates for the Reclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reclamationStates(): HasMany
    {
        return $this->hasMany(ReclamationState::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the categorieReclamation that owns the Reclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categorieReclamation(): BelongsTo
    {
        return $this->belongsTo(CategorieReclamation::class);
    }

    public function getNumberAttribute()
    {
        return $this->categorieReclamation->application->code . "-" . $this->numero;
    }

    public function priorite(): BelongsTo
    {
        return $this->belongsTo(Priorite::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the state that owns the Reclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get all of the comments for the Reclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('comment_id');
    }

    /**
     * Get the latestState associated with the Reclamation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestReclamationState(): HasOne
    {
        return $this->hasOne(ReclamationState::class)->latest();
    }
}
