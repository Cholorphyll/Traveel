<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default & Fallback Locale
    |--------------------------------------------------------------------------
    |
    | default_locale: The locale (language-region) that will be used when we
    | cannot determine the visitor’s location.
    | fallback_locale: Used whenever translations are missing.
    |
    */
    'default_locale' => env('APP_DEFAULT_LOCALE', 'en-US'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en-US'),
    'default_language' => env('APP_DEFAULT_LANGUAGE', 'en'),
    'default_country' => env('APP_DEFAULT_COUNTRY', 'US'),

    /*
    |--------------------------------------------------------------------------
    | GeoIP cache lifetime (seconds)
    |--------------------------------------------------------------------------
    */
    'cache_ttl' => (int) env('GEOIP_CACHE_TTL', 900),

    /*
    |--------------------------------------------------------------------------
    | Country → locale map
    |--------------------------------------------------------------------------
    |
    | Extend this mapping as you add more localized experiences. The locale
    | string should follow IETF BCP47 (language-REGION).
    |
    */
    'country_locale_map' => [
        'US' => 'en-US',
        'CA' => 'en-CA',
        'GB' => 'en-GB',
        'AU' => 'en-AU',
        'NZ' => 'en-NZ',
        'IN' => 'en-IN',
        'AE' => 'en-AE',
        'FR' => 'fr-FR',
        'ES' => 'es-ES',
        'DE' => 'de-DE',
        'IT' => 'it-IT',
        'BR' => 'pt-BR',
        'MX' => 'es-MX',
        'JP' => 'ja-JP',
        'KR' => 'ko-KR',
        'CN' => 'zh-CN',
        'SG' => 'en-SG',
        'ID' => 'id-ID',
        'MY' => 'ms-MY',
        'TH' => 'th-TH',
    ],

    /*
    |--------------------------------------------------------------------------
    | GeoIP provider settings
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'ipapi' => [
            'base_url' => env('GEOIP_IPAPI_BASE_URL', 'https://ipapi.co'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain locale overrides
    |--------------------------------------------------------------------------
    |
    | Map hostnames (supports wildcards via fnmatch) to a forced locale. This
    | runs before GeoIP detection so that ccTLDs like travell.co.in default to
    | en-IN even when IP geolocation fails.
    |
    */
    'domain_locale_map' => [
        '*.travell.co.in' => 'en-IN',
        'travell.co.in' => 'en-IN',
        '*.travell.in' => 'en-IN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatic translation widget configuration
    |--------------------------------------------------------------------------
    |
    | When enabled we will inject the translation bootstrapper into HTML
    | responses to automatically translate the rendered page.
    |
    */
    'translation_widget' => [
        'enabled' => env('AUTO_TRANSLATE_ENABLED', true),
        'source_language' => env('AUTO_TRANSLATE_SOURCE', 'en'),
    ],
];
