<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'brand',
        'model',
        'year',
        'price',
        'os',
        'condition',
        'color',
        'city',
        'description',
        'contact_phone',
        'contact_email',
        'featured',
        'status',
        'published_at'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(ListingImage::class)->orderBy('ordering');
    }
    public function primaryImage()
    {
        return $this->hasOne(ListingImage::class)->where('is_primary', true);
    }

    public function scopeActive(Builder $q)
    {
        return $q->where('status', 'active');
    }

    public function scopeFilters(Builder $q, array $f)
    {
        return $q
            ->when($f['q'] ?? null, fn($qq, $qStr) =>
                $qq->where(fn($w) => $w->where('title', 'like', "%$qStr%")
                    ->orWhere('description', 'like', "%$qStr%")))
            ->when($f['brand'] ?? null, fn($qq, $v) => $qq->where('brand', $v))
            ->when($f['model'] ?? null, fn($qq, $v) => $qq->where('model', 'like', "%$v%"))
            ->when($f['os'] ?? null, fn($qq, $v) => $qq->where('os', $v))
            ->when($f['condition'] ?? null, fn($qq, $v) => $qq->where('condition', $v))
            ->when($f['city'] ?? null, fn($qq, $v) => $qq->where('city', $v))
            ->when($f['min_price'] ?? null, fn($qq, $v) => $qq->where('price', '>=', $v))
            ->when($f['max_price'] ?? null, fn($qq, $v) => $qq->where('price', '<=', $v))
            ->when($f['year'] ?? null, fn($qq, $v) => $qq->where('year', $v))
            ->when($f['seller'] ?? null, fn($qq, $id) => $qq->where('user_id', $id));
    }
}
