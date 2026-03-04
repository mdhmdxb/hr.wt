<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected $casts = [
        'value' => 'array',
    ];

    /** Get a setting value (global or for company). Cached. */
    public static function getValue(string $key, ?int $companyId = null): mixed
    {
        $effectiveKey = $companyId ? "{$key}_company_{$companyId}" : $key;
        $cacheKey = 'setting.' . ($companyId ?? 'global') . '.' . $key;
        return Cache::remember($cacheKey, 3600, function () use ($effectiveKey) {
            $row = self::where('key', $effectiveKey)->first();
            if (! $row) {
                return null;
            }
            $v = $row->getRawOriginal('value') ?? $row->value;
            if (is_string($v)) {
                return json_decode($v, true);
            }
            return $v;
        });
    }

    /** Set a setting value and clear cache. */
    public static function setValue(string $key, mixed $value, ?int $companyId = null): void
    {
        $effectiveKey = $companyId ? "{$key}_company_{$companyId}" : $key;
        self::updateOrCreate(
            ['key' => $effectiveKey],
            ['value' => is_array($value) ? $value : [$value], 'group' => $key === 'modules' ? 'modules' : 'general']
        );
        Cache::forget('setting.' . ($companyId ?? 'global') . '.' . $key);
    }

    /** List of module keys that can be toggled by owner. */
    public static function moduleKeys(): array
    {
        return [
            'employees' => 'Employees',
            'attendance' => 'Attendance',
            'leave' => 'Leave',
            'payroll' => 'Payroll',
            'assets' => 'Assets',
            'recruitment' => 'Recruitment',
            'documents' => 'Documents',
            'reports' => 'Reports',
            'templates' => 'Templates',
            'designations' => 'Designations',
            'companies' => 'Companies',
            'branches' => 'Branches',
            'sites' => 'Sites',
            'projects' => 'Projects',
        ];
    }

    /** Module keys grouped for Owner portal (group label => array of module keys). */
    public static function moduleGroups(): array
    {
        $keys = self::moduleKeys();
        return [
            'People' => ['employees', 'designations', 'documents', 'recruitment'],
            'Time' => ['attendance', 'leave'],
            'Finance' => ['payroll'],
            'Organization' => ['companies', 'branches', 'sites', 'projects'],
            'Assets & reports' => ['assets', 'reports', 'templates'],
        ];
    }

    /** Check if a module/feature is enabled (global: company_id null). */
    public static function isModuleEnabled(string $module, ?int $companyId = null): bool
    {
        $enabled = self::getValue('modules', $companyId);
        if (! is_array($enabled)) {
            return true; // default all on
        }
        return in_array($module, $enabled, true);
    }
}
