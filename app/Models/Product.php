<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_per_day',
        'stock',
        'condition',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Hitung sisa stok tersedia untuk rentang tanggal tertentu berdasarkan pesanan aktif.
     */
    public function getAvailableStockForDateRange(string $startDate, string $endDate): int
    {
        $bookedQuantity = (int) RentalItem::where('product_id', $this->id)
            ->whereHas('rental', function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['confirmed', 'preparing', 'ready_pickup', 'in_use'])
                    ->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->sum('quantity');

        return max(0, $this->stock - $bookedQuantity);
    }
}
