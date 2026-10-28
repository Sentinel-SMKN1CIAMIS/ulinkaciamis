<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'created_by',
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'kecamatan',
        'phone',
        'website',
        'opening_hours',
        'closing_hours',
        'has_wifi',
        'price_range',
        'image',
        'is_active',
        'views',
        'rating',
        'features',
        'moods',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'has_wifi' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
        'rating' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, '/ulinkaciamis/')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'assets/')) {
            return '/ulinkaciamis/'.$this->image;
        }

        return asset('storage/'.$this->image);
    }

    // Aliases for compatibility
    public function getNamaAttribute(): string
    {
        return $this->name ?? '';
    }

    public function getAlamatAttribute(): ?string
    {
        return $this->address;
    }

    public function getDeskripsiAttribute(): ?string
    {
        return $this->description;
    }

    public function getHargaAttribute(): ?string
    {
        return $this->price_range;
    }

    public function getWifiAttribute(): bool
    {
        return (bool) $this->has_wifi;
    }

    public function getFotoAttribute(): ?string
    {
        return $this->image;
    }

    public function getNoTeleponAttribute(): ?string
    {
        return $this->phone;
    }

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'aktif' : 'nonaktif';
    }
}
