<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Voter extends Authenticatable
{
    use Notifiable;

    protected $guard = 'voter';

    protected $table = 'voters';

    protected $fillable = [
        'userID',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'grade_level_id',
        'section_id', 
        'photo_path',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }
}