<?php

namespace App\Models;

use Database\Factories\RentalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    /** @use HasFactory<RentalFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'rental_code',
        'start_date',
        'end_date',
        'duration_days',
        'total_price',
        'promo_code',
        'discount_amount',
        'status',
        'payment_method',
        'payment_proof',
        'notes',
        'admin_notes',
        'item_condition_after',
        'returned_at',
        'return_photo',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'returned_at' => 'datetime',
            'duration_days' => 'integer',
            'total_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the user that booked the rental.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rental items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    /**
     * Get all fines recorded for this rental.
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Get the customer verification documents for this rental.
     */
    public function verification(): HasOne
    {
        return $this->hasOne(RentalVerification::class);
    }

    /**
     * Check if rental has approved identity verification.
     */
    public function hasApprovedVerification(): bool
    {
        return $this->verification !== null
            && $this->verification->verification_status === RentalVerification::STATUS_DISETUJUI;
    }

    /**
     * Get all payment attempts recorded for this rental.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment attempt for this rental.
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Get customer reviews linked to this rental.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Total amount of fines for this rental.
     */
    public function getTotalFinesAttribute(): float
    {
        if ($this->relationLoaded('fines')) {
            return (float) $this->fines->sum('amount');
        }

        return (float) $this->fines()->sum('amount');
    }

    /**
     * Get the late fine record if any.
     */
    public function getLateFineAttribute(): ?Fine
    {
        if ($this->relationLoaded('fines')) {
            return $this->fines->firstWhere('type', Fine::TYPE_LATE);
        }

        return $this->fines()->where('type', Fine::TYPE_LATE)->first();
    }

    /**
     * Get all damage fine records for this rental.
     */
    public function getDamageFinesAttribute()
    {
        if ($this->relationLoaded('fines')) {
            return $this->fines->where('type', Fine::TYPE_DAMAGE);
        }

        return $this->fines()->where('type', Fine::TYPE_DAMAGE)->get();
    }

    /**
     * Human readable status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'waiting_verification' => 'Menunggu Verifikasi Admin',
            'confirmed' => 'Penyewaan Dikonfirmasi',
            'preparing' => 'Barang Sedang Disiapkan',
            'ready_pickup' => 'Siap Diambil / Diterima',
            'in_use' => 'Barang Sedang Digunakan',
            'returned' => 'Barang Dikembalikan',
            'completed' => 'Transaksi Selesai',
            'cancelled' => 'Dibatalkan',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Status badge color class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending_payment' => 'bg-amber-100 text-amber-800 border-amber-200',
            'waiting_verification' => 'bg-blue-100 text-blue-800 border-blue-200',
            'confirmed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'preparing' => 'bg-purple-100 text-purple-800 border-purple-200',
            'ready_pickup' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            'in_use' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'returned' => 'bg-teal-100 text-teal-800 border-teal-200',
            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'cancelled', 'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
