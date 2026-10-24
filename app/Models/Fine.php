<?php

namespace App\Models;

use Database\Factories\FineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    /** @use HasFactory<FineFactory> */
    use HasFactory;

    public const TYPE_LATE = 'late';

    public const TYPE_DAMAGE = 'damage';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rental_id',
        'rental_item_id',
        'type',
        'amount',
        'days_late',
        'rate_per_day',
        'reason',
        'assessed_by',
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
            'days_late' => 'integer',
            'rate_per_day' => 'decimal:2',
        ];
    }

    /**
     * Get the rental transaction that owns the fine.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Get the specific rental item associated with this fine (e.g. for damage fines).
     */
    public function rentalItem(): BelongsTo
    {
        return $this->belongsTo(RentalItem::class);
    }

    /**
     * Get the admin user who assessed the fine.
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    /**
     * Human-readable label for fine type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_LATE => 'Denda Keterlambatan',
            self::TYPE_DAMAGE => 'Denda Kerusakan Barang',
            default => ucfirst($this->type),
        };
    }

    /**
     * Check if fine is for late return.
     */
    public function isLate(): bool
    {
        return $this->type === self::TYPE_LATE;
    }

    /**
     * Check if fine is for damage.
     */
    public function isDamage(): bool
    {
        return $this->type === self::TYPE_DAMAGE;
    }
}
