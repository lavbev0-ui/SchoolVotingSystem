<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeLevel extends Model
{
    protected $fillable = [
        'name',
        'category',
    ];

    /**
     * Kunin ang mga unique categories mula sa database.
     */
    public static function getCategories()
    {
        return self::select('category')->distinct()->pluck('category');
    }

    /**
     * Relationship: Isang Grade Level ay may maraming Sections.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /**
     * FIX: Idinagdag ang relationship para sa Voters.
     * Ito ang kailangan para sa Bar Chart analytics sa dashboard.
     */
    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class);
    }
}