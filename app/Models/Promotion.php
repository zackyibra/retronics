<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'discount_value',
        'max_discount',
        'min_transaction',
        'quota_total',
        'quota_used',
        'start_date',
        'end_date',
        'category_id',
        'image',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'min_transaction' => 'decimal:2',
            'quota_total' => 'integer',
            'quota_used' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi opsional ke kategori spesifik.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope promo yang aktif dan masih dalam periode berlaku.
     */
    public function scopeActive(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereColumn('quota_used', '<', 'quota_total');
    }

    /**
     * Sisa kuota voucher yang tersedia.
     */
    public function getQuotaRemainingAttribute(): int
    {
        return max(0, $this->quota_total - $this->quota_used);
    }

    /**
     * Label representasi diskon.
     */
    public function getDiscountLabelAttribute(): string
    {
        if ($this->type === 'percentage') {
            return ((int) $this->discount_value == $this->discount_value ? (int) $this->discount_value : $this->discount_value).'%';
        }

        return 'Rp'.number_format($this->discount_value, 0, ',', '.');
    }

    /**
     * Format teks syarat transaksi minimum.
     */
    public function getFormattedMinTransactionAttribute(): string
    {
        return 'Rp'.number_format($this->min_transaction, 0, ',', '.');
    }

    /**
     * Periksa apakah voucher valid untuk total belanja tertentu.
     */
    public function isValidForAmount(float $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = Carbon::today();
        if ($today->lt($this->start_date) || $today->gt($this->end_date)) {
            return false;
        }

        if ($this->quota_used >= $this->quota_total) {
            return false;
        }

        if ($subtotal < (float) $this->min_transaction) {
            return false;
        }

        return true;
    }

    /**
     * Hitung besaran nominal potongan diskon berdasarkan subtotal sewa.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->isValidForAmount($subtotal)) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discount = $subtotal * ((float) $this->discount_value / 100);
            if ($this->max_discount !== null && (float) $this->max_discount > 0) {
                $discount = min($discount, (float) $this->max_discount);
            }

            return max(0.0, round($discount, 2));
        }

        return max(0.0, min((float) $this->discount_value, $subtotal));
    }
}
