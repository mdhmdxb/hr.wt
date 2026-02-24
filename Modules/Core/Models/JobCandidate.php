<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCandidate extends Model
{
    protected $fillable = ['job_opening_id', 'name', 'email', 'phone', 'stage', 'notes', 'interview_at'];

    protected $casts = [
        'interview_at' => 'datetime',
    ];

    public const STAGE_APPLIED = 'applied';
    public const STAGE_SHORTLISTED = 'shortlisted';
    public const STAGE_INTERVIEW = 'interview';
    public const STAGE_OFFERED = 'offered';
    public const STAGE_HIRED = 'hired';
    public const STAGE_REJECTED = 'rejected';

    public static function stageOptions(): array
    {
        return [
            self::STAGE_APPLIED => 'Applied',
            self::STAGE_SHORTLISTED => 'Shortlisted',
            self::STAGE_INTERVIEW => 'Interview',
            self::STAGE_OFFERED => 'Offered',
            self::STAGE_HIRED => 'Hired',
            self::STAGE_REJECTED => 'Rejected',
        ];
    }

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }
}
