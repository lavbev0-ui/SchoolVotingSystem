<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'title', 
        'description',
        'start_at', 
        'end_at', 
        'eligibility_type', 
        'eligibility_metadata',
        'is_active',
    ];

    protected $casts = [
        'start_at' => 'datetime',   
        'end_at' => 'datetime',
        'eligibility_metadata' => 'array', 
        'is_active' => 'boolean',
    ];

    /**
     * I-append ang status para madaling ma-access sa Blade o JSON.
     */
    protected $appends = ['status'];

    // --- SCOPES ---

    /**
     * Scope para sa mga kasalukuyang tumatakbong eleksyon.
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true)
                     ->where('start_at', '<=', now())
                     ->where('end_at', '>=', now());
    }

    /**
     * Scope para sa mga natapos na eleksyon.
     */
    public function scopeCompleted(Builder $query)
    {
        return $query->where('end_at', '<=', now());
    }

    // --- ACCESSORS ---

    /**
     * Status Accessor (Active, Upcoming, Ended).
     */
    public function getStatusAttribute(): string
    {
        $now = now();
        if ($now->between($this->start_at, $this->end_at)) {
            return 'active';
        }
        if ($now->lt($this->start_at)) {
            return 'upcoming';
        }
        return 'ended';
    }

    // --- RELATIONSHIPS ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function positions(): HasMany
    {
        // Mas mainam na naka-order ang positions base sa pagkakalikha o custom order
        return $this->hasMany(Position::class)->orderBy('id', 'asc');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Election -> Positions -> Candidates.
     */
    public function candidates(): HasManyThrough
    {
        return $this->hasManyThrough(Candidate::class, Position::class);
    }
}