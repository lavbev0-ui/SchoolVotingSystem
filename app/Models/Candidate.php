<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'position_id', 
        'name', 
        'grade_level_id', 
        'photo_path', 
        'bio',       
        'platform',
        'section_id',
        'party',  
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}