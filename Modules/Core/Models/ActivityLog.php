<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, ?string $description = null, ?int $userId = null): self
    {
        $uid = $userId ?? auth()->id();
        // File-based owner has no integer id; store null to avoid DB error
        if (auth()->check() && auth()->user() instanceof FileBasedOwner) {
            $uid = null;
        }
        return static::create([
            'user_id' => $uid,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
