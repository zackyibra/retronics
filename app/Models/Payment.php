<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    public const STATUS_WAITING_VERIFICATION = 'waiting_verification';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rental_id',
        'payment_code',
        'amount',
        'payment_method',
        'payment_date',
        'payment_proof',
        'status',
        'verified_by',
        'verified_at',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the rental transaction that owns the payment.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Get the admin user who verified the payment.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Alias for verifiedBy relationship.
     */
    public function verifier(): BelongsTo
    {
        return $this->verifiedBy();
    }

    /**
     * Scope query to waiting verification payments.
     */
    public function scopeWaitingVerification(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_WAITING_VERIFICATION);
    }

    /**
     * Scope query to successful payments.
     */
    public function scopeSuccess(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope query to rejected payments.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Human-readable label for payment status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING_VERIFICATION => 'Menunggu Verifikasi',
            self::STATUS_SUCCESS => 'Berhasil',
            self::STATUS_REJECTED => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Badge CSS class for payment status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING_VERIFICATION => 'bg-blue-100 text-blue-800 border-blue-200',
            self::STATUS_SUCCESS => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::STATUS_REJECTED => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    /**
     * Human-readable label for payment method.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'transfer_bca' => 'Transfer Bank BCA',
            'transfer_mandiri' => 'Transfer Bank Mandiri',
            'qris' => 'QRIS Instan',
            'cash_store' => 'Tunai di Kasir Toko',
            default => strtoupper(str_replace('_', ' ', $this->payment_method)),
        };
    }
}
