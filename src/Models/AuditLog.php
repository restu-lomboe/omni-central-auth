<?php

namespace DeveloperAwam\OmniCentralAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'omni_audit_logs';

    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'user_agent',
        'client_app',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('omni-central-auth.user_model'));
    }

    /**
     * Catat event baru ke audit log.
     */
    public static function record(string $event, array $metadata = []): static
    {
        return static::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'client_app' => request()->header('X-Client-App'),
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Hapus log lama berdasarkan retention policy.
     */
    public static function pruneOldLogs(): int
    {
        $days = config('omni-central-auth.audit.retention_days');

        if (! $days) {
            return 0;
        }

        return static::where('occurred_at', '<', now()->subDays($days))->delete();
    }
}
