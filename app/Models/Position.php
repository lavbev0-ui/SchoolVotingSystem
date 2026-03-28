<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'title',
        'max_selection', // Tugma sa iyong Controller logic
        'max_votes',     // Tugma sa iyong DB structure
        'description',
    ];

    /**
     * Relationship sa Election.
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Relationship sa Candidates. 
     * Ito ang "tulay" para lumitaw ang mga kandidato sa iyong dashboard.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * Relationship sa Votes para sa counting at analytics.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}