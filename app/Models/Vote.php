<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'position_id',
        'candidate_id',
        'voter_id',
    ];

    /**
     * Ugnayan sa Election model.
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Ugnayan sa Position model.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Ugnayan sa Candidate model.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Ugnayan sa Voter model.
     */
    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class);
    }
}