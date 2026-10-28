<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'icon_color',
        'icon_type',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function activeLocations(): HasMany
    {
        return $this->hasMany(Location::class)->where('is_active', true);
    }

    public function getNamaAttribute(): string
    {
        return $this->name ?? '';
    }

    public function getWarnaAttribute(): string
    {
        return $this->icon_color ?? '#3B82F6';
    }

    public function getUrutanAttribute(): int
    {
        return (int) $this->sort_order;
    }
}
