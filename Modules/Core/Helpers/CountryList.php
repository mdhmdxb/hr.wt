<?php

namespace Modules\Core\Helpers;

class CountryList
{
    /** ISO 3166-1 alpha-2 codes => country name (for company country & public holidays). */
    public static function codes(): array
    {
        return [
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'BH' => 'Bahrain',
            'KW' => 'Kuwait',
            'OM' => 'Oman',
            'QA' => 'Qatar',
            'EG' => 'Egypt',
            'JO' => 'Jordan',
            'IN' => 'India',
            'PK' => 'Pakistan',
            'BD' => 'Bangladesh',
            'PH' => 'Philippines',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'SG' => 'Singapore',
            'MY' => 'Malaysia',
            'ID' => 'Indonesia',
            'LK' => 'Sri Lanka',
            'NP' => 'Nepal',
            'NG' => 'Nigeria',
            'ZA' => 'South Africa',
            'KE' => 'Kenya',
            'OTHER' => 'Other',
        ];
    }
}
