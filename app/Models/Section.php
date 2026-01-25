<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['grade_level_id', 'name'];

    // A Section belongs to one Grade Level
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }
    
    // Optional: If you want to link Students to Sections later
    public function students()
    {
        return $this->hasMany(User::class);
    }
}