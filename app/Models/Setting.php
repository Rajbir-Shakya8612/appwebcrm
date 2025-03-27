<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, $default = null)
    {
        return self::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Set or update a setting value.
     */
    public static function setValue(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
