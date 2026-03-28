<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'voter_id', 
        'position_id',
        'first_name',
        'middle_name',
        'last_name',
        'grade_level_id', 
        'section_id',     
        'party',
        'manifesto',      
        'bio',            
        'photo_path',
    ];

    protected $appends = ['full_name', 'platform', 'profile'];

    // --- RELATIONSHIPS ---

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Tallying Connection: Sinisiguro nito na mabibilang ang boto.
     * Dapat ang 'votes' table ay may 'candidate_id' column.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'candidate_id');
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class, 'voter_id');
    }

    // --- ACCESSORS ---

    protected function profile(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => !empty(trim($attributes['bio'] ?? '')) 
                ? $attributes['bio'] 
                : 'No candidate profile provided.',
        );
    }

    protected function platform(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => !empty(trim($attributes['manifesto'] ?? '')) 
                ? $attributes['manifesto'] 
                : 'No platform provided.',
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Mas mabilis na logic gamit ang $attributes array
                if (!empty($attributes['first_name'])) {
                    $middle = !empty($attributes['middle_name']) ? $attributes['middle_name'] . ' ' : '';
                    return trim("{$attributes['first_name']} {$middle}{$attributes['last_name']}");
                }
                
                if ($this->relationLoaded('voter') && $this->voter) {
                    $vMiddle = !empty($this->voter->middle_name) ? $this->voter->middle_name . ' ' : '';
                    return trim("{$this->voter->first_name} {$vMiddle}{$this->voter->last_name}");
                }

                return "Candidate #{$this->id}";
            }
        );
    }
}