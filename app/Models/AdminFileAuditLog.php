<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminFileAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'file_type',
        'file_path',
        'file_name',
        'related_model_type',
        'related_model_id',
        'reference_code',
        'reason',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Admin yang melakukan penghapusan file.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Catat aktivitas penghapusan berkas oleh administrator.
     */
    public static function logDeletion(
        int $adminId,
        string $fileType,
        string $filePath,
        ?string $relatedModelType = null,
        ?int $relatedModelId = null,
        ?string $referenceCode = null,
        ?string $reason = null,
        ?string $fileName = null
    ): self {
        return self::create([
            'admin_id' => $adminId,
            'file_type' => $fileType,
            'file_path' => $filePath,
            'file_name' => $fileName ?? basename($filePath),
            'related_model_type' => $relatedModelType,
            'related_model_id' => $relatedModelId,
            'reference_code' => $referenceCode,
            'reason' => $reason,
            'deleted_at' => now(),
        ]);
    }
}
