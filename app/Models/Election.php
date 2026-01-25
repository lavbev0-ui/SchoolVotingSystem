<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\GradeLevel; 
use App\Models\Section;

class Election extends Model
{

    protected $fillable = [
        'user_id', 
        'title', 
        'bio',
        'start_at', 
        'end_at', 
        'eligibility_type', 
        'eligibility_metadata',
        'status'
    ];

    protected $casts = [
        'start_at' => 'datetime',   
        'end_at' => 'datetime',
        'eligibility_metadata' => 'array', 
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function candidates(): HasManyThrough
    {
        return $this->hasManyThrough(Candidate::class, Position::class);
    }

    // Relationship to Grade Levels
    public function gradeLevels()
    {
        // Assuming you have a pivot table named 'election_grade_level'
        return $this->belongsToMany(GradeLevel::class, 'election_grade_level');
    }

    // Relationship to Sections
    public function sections()
    {
        // Assuming you have a pivot table named 'election_section'
        return $this->belongsToMany(Section::class, 'election_section');
    }

}