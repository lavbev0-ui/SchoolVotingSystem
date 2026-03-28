<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Voter extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'voter';
    protected $table = 'voters';

    // ✅ Disable remember token — walang remember_token column sa voters table
    public $rememberTokenName = false;

    protected $fillable = [
        'student_id', 
        'userID',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',         
        'email',
        'password',
        'grade_level_id',
        'section_id', 
        'photo_path',
        'is_active',
        'password_changed',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'password'         => 'hashed',
        'password_changed' => 'boolean',
    ];

    protected $appends = ['full_name', 'photo_url'];

    // --- RELATIONSHIPS ---

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'voter_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id');
    }

    // --- ACCESSORS ---

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} " . ($this->middle_name ? "{$this->middle_name} " : "") . "{$this->last_name} " . ($this->suffix ?? "")),
        );
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
                    return asset('storage/' . $this->photo_path);
                }
                return asset('images/default-avatar.png');
            },
        );
    }

    // --- HELPER METHODS ---

    public function hasVotedIn($electionId): bool
    {
        return $this->votes()->where('election_id', $electionId)->exists();
    }
}