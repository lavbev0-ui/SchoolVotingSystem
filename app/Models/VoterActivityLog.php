<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoterActivityLog extends Model
{
    protected $fillable = ['voter_id', 'action', 'description'];

    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class, 'voter_id');
    }
}