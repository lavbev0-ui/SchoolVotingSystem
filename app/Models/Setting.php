<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'setting_key',
        'value',
        'label',
        'type',
    ];

    /**
     * Create or update a setting by key
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['value' => $value]
        );
    }
}
