<?php

namespace App\Models;

use Database\Factories\RentalVerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalVerification extends Model
{
    /** @use HasFactory<RentalVerificationFactory> */
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_PERLU_REVISI = 'perlu_revisi';

    /**
     * Threshold transaksi yang mewajibkan Bea Meterai (UU No. 10/2020: Rp 5.000.000).
     */
    public const STAMP_DUTY_THRESHOLD = 5000000;

    public const STAMP_DUTY_FEE = 10000;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rental_id',
        'user_id',
        'id_card_number',
        'id_card_file',
        'birth_date',
        'rental_purpose',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'signature_data',
        'agreement_signed_at',
        'agreement_ip_address',
        'requires_stamp_duty',
        'stamp_duty_amount',
        'verification_status',
        'verified_by',
        'verified_at',
        'admin_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'agreement_signed_at' => 'datetime',
            'verified_at' => 'datetime',
            'requires_stamp_duty' => 'boolean',
            'stamp_duty_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the rental transaction associated with this verification.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Get the user who owns this verification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified this document.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Status checkers.
     */
    public function isPending(): bool
    {
        return $this->verification_status === self::STATUS_MENUNGGU;
    }

    public function isApproved(): bool
    {
        return $this->verification_status === self::STATUS_DISETUJUI;
    }

    public function isRejected(): bool
    {
        return $this->verification_status === self::STATUS_DITOLAK;
    }

    public function needsRevision(): bool
    {
        return $this->verification_status === self::STATUS_PERLU_REVISI;
    }

    /**
     * Human-readable label for verification status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->verification_status) {
            self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
            self::STATUS_DISETUJUI => 'Dokumen Disetujui',
            self::STATUS_DITOLAK => 'Dokumen Ditolak',
            self::STATUS_PERLU_REVISI => 'Perlu Revisi Dokumen',
            default => ucfirst(str_replace('_', ' ', $this->verification_status)),
        };
    }

    /**
     * Tailwind CSS badge classes.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->verification_status) {
            self::STATUS_MENUNGGU => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_DISETUJUI => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_DITOLAK => 'bg-rose-50 text-rose-700 border-rose-200',
            self::STATUS_PERLU_REVISI => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
