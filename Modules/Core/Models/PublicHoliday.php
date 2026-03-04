<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    protected $fillable = ['name', 'date', 'end_date', 'country_code'];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
    ];

    /** Effective end date: end_date if set, otherwise date (single day). */
    public function getEffectiveEndDateAttribute(): \Carbon\Carbon
    {
        return $this->end_date ?? $this->date;
    }

    /** Check if a given date falls within this holiday range (inclusive). */
    public function coversDate(\Carbon\Carbon $d): bool
    {
        $start = $this->date->copy()->startOfDay();
        $end = $this->getEffectiveEndDateAttribute()->copy()->endOfDay();
        return $d->between($start, $end);
    }

    /** Check if a given date is a public holiday (global, any country). Supports date ranges. */
    public static function isHoliday(\Carbon\Carbon $date): bool
    {
        $d = $date->format('Y-m-d');
        return self::query()
            ->where(function ($q) use ($d) {
                $q->whereDate('date', '<=', $d)
                    ->where(function ($q2) use ($d) {
                        $q2->whereNotNull('end_date')->whereDate('end_date', '>=', $d)
                            ->orWhereNull('end_date')->whereDate('date', $d);
                    });
            })
            ->exists();
    }

    /** Get holiday name for a date, or null. */
    public static function getHolidayNameFor(\Carbon\Carbon $date): ?string
    {
        $holidays = self::all();
        foreach ($holidays as $h) {
            if ($h->coversDate($date)) {
                return $h->name;
            }
        }
        return null;
    }

    /** Scope: holidays for a country (null = global, or match country_code). */
    public function scopeForCountry($query, ?string $countryCode)
    {
        if ($countryCode === null || $countryCode === '') {
            return $query;
        }
        return $query->where(function ($q) use ($countryCode) {
            $q->whereNull('country_code')->orWhere('country_code', $countryCode);
        });
    }

    /** Holidays that cover any day in the given month (for calendar). */
    public static function forMonth(int $year, int $month, ?string $countryCode = null)
    {
        $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $q = self::query()
            ->where(function ($q) use ($start, $end) {
                $q->whereDate('date', '<=', $end)
                    ->where(function ($q2) use ($start) {
                        $q2->whereNotNull('end_date')->whereDate('end_date', '>=', $start)
                            ->orWhereNull('end_date')->whereDate('date', '>=', $start);
                    });
            });
        if ($countryCode !== null && $countryCode !== '') {
            $q->forCountry($countryCode);
        }
        return $q->orderBy('date')->get();
    }

    /** Upcoming holidays (next 60 days) for dashboard card. */
    public static function upcoming(\Carbon\Carbon $from, int $days = 60, ?string $countryCode = null)
    {
        $to = $from->copy()->addDays($days);
        $q = self::query()
            ->where(function ($q) use ($from, $to) {
                $q->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)
                    ->orWhereNotNull('end_date')->whereDate('end_date', '>=', $from)->whereDate('date', '<=', $to);
            })
            ->orderBy('date');
        if ($countryCode !== null && $countryCode !== '') {
            $q->forCountry($countryCode);
        }
        return $q->get();
    }
}
