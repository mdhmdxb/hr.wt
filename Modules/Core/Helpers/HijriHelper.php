<?php

namespace Modules\Core\Helpers;

use Carbon\Carbon;

/**
 * Simple Gregorian ↔ Hijri (Islamic) date conversion for display.
 * Approximation; for religious purposes use authoritative sources.
 */
class HijriHelper
{
    public static function intPart(float $n): int
    {
        return $n < -0.0000001 ? (int) ceil($n - 0.0000001) : (int) floor($n + 0.0000001);
    }

    /** Convert Gregorian date to Hijri [year, month, day]. */
    public static function gregorianToHijri(int $y, int $m, int $d): array
    {
        if ($y <= 1582 && ($y < 1582 || $m < 10 || ($m == 10 && $d <= 14))) {
            $jd = 367 * $y - self::intPart((7 * ($y + 5001 + self::intPart(($m - 9) / 7))) / 4)
                + self::intPart((275 * $m) / 9) + $d + 1729777;
        } else {
            $jd = self::intPart((1461 * ($y + 4800 + self::intPart(($m - 14) / 12))) / 4)
                + self::intPart((367 * ($m - 2 - 12 * self::intPart(($m - 14) / 12))) / 12)
                - self::intPart((3 * self::intPart(($y + 4900 + self::intPart(($m - 14) / 12)) / 100)) / 4)
                + $d - 32075;
        }

        $l = $jd - 1948440 + 10632;
        $n = self::intPart(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = (self::intPart((10985 - $l) / 5316)) * (self::intPart((50 * $l) / 17719))
            + (self::intPart($l / 5670)) * (self::intPart((43 * $l) / 15238));
        $l = $l - (self::intPart((30 - $j) / 15)) * (self::intPart((17719 * $j) / 50))
            - (self::intPart($j / 16)) * (self::intPart((15238 * $j) / 43)) + 29;
        $month = self::intPart((24 * $l) / 709);
        $day = $l - self::intPart((709 * $month) / 24);
        $year = 30 * $n + $j - 30;

        return [(int) $year, (int) $month, (int) $day];
    }

    /** Get Hijri date string for a Carbon instance (e.g. "12 Shaʻban 1447"). */
    public static function format(Carbon $date): string
    {
        [$y, $m, $d] = self::gregorianToHijri(
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j')
        );
        $monthName = self::monthName($m);
        return $d . ' ' . $monthName . ' ' . $y;
    }

    /** Short form for calendar cells (e.g. "12/8"). */
    public static function short(Carbon $date): string
    {
        [$y, $m, $d] = self::gregorianToHijri(
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j')
        );
        return $d . '/' . $m;
    }

    public static function monthName(int $month): string
    {
        $names = [
            1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiʻ I', 4 => 'Rabiʻ II',
            5 => 'Jumada I', 6 => 'Jumada II', 7 => 'Rajab', 8 => 'Shaʻban',
            9 => 'Ramadan', 10 => 'Shawwal', 11 => 'Dhu al-Qidah', 12 => 'Dhu al-Hijjah',
        ];
        return $names[$month] ?? (string) $month;
    }
}
