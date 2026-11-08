<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'attempts',
        'is_verified',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_verified' => 'boolean',
            'attempts' => 'integer',
        ];
    }

    /**
     * Otomatis hash kode OTP sebelum disimpan ke database jika belum ter-hash.
     */
    public function setOtpAttribute(string $value): void
    {
        $this->attributes['otp'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Periksa apakah OTP telah kedaluwarsa.
     */
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Periksa apakah telah melebihi batas toleransi kesalahan kode.
     */
    public function hasExceededAttempts(int $max = 5): bool
    {
        return $this->attempts >= $max;
    }

    /**
     * Validasi kode OTP yang dimasukkan menggunakan Hash::check.
     */
    public function isValid(string $inputOtp): bool
    {
        if ($this->isExpired() || $this->hasExceededAttempts() || $this->is_verified) {
            return false;
        }

        return Hash::check(trim($inputOtp), (string) $this->otp);
    }
}
