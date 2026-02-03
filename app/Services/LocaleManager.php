<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocaleManager
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('locale');
    }

    /**
     * Resolve locale information for the given IP or forced locale string.
     */
    public function resolve(?string $ip, ?string $forcedLocale = null): array
    {
        if ($forcedLocale && $this->isValidLocale($forcedLocale)) {
            return $this->buildPayload($forcedLocale);
        }

        if (!$this->isPublicIp($ip)) {
            return $this->defaults();
        }

        $cacheKey = sprintf('geoip_locale_%s', $ip);

        return Cache::remember(
            $cacheKey,
            $this->config['cache_ttl'],
            fn () => $this->lookupLocaleForIp($ip) ?? $this->defaults()
        );
    }

    public function defaults(): array
    {
        return $this->buildPayload($this->config['default_locale'], [
            'country' => $this->config['default_country'],
            'ip' => null,
            'source_language' => $this->config['translation_widget']['source_language'] ?? $this->config['default_language'],
        ], false);
    }

    protected function lookupLocaleForIp(?string $ip): ?array
    {
        if (!$ip) {
            return null;
        }

        $baseUrl = rtrim($this->config['providers']['ipapi']['base_url'] ?? '', '/');
        if (empty($baseUrl)) {
            return null;
        }

        try {
            $response = Http::timeout(4)->retry(2, 200)->get("{$baseUrl}/{$ip}/json/");
        } catch (\Throwable $th) {
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $payload = $response->json();
        $country = strtoupper($payload['country_code'] ?? '') ?: $this->config['default_country'];
        $locale = $this->config['country_locale_map'][$country] ?? $this->config['default_locale'];

        return $this->buildPayload($locale, [
            'country' => $country,
            'region' => strtoupper($payload['region_code'] ?? '') ?: null,
            'ip' => $ip,
        ]);
    }

    protected function splitLocale(string $locale): array
    {
        $parts = explode('-', $locale);

        return [
            'language' => strtolower($parts[0] ?? $this->config['default_language']),
            'region' => strtoupper($parts[1] ?? $this->config['default_country']),
        ];
    }

    protected function buildPayload(string $locale, array $overrides = [], bool $computeTranslation = true): array
    {
        $locale = $this->normalizeLocale($locale);
        $parts = $this->splitLocale($locale);
        $sourceLanguage = $this->config['translation_widget']['source_language'] ?? $this->config['default_language'];

        $data = array_merge([
            'locale' => $locale,
            'language' => $parts['language'],
            'region' => $parts['region'],
            'country' => $parts['region'],
            'ip' => null,
            'source_language' => $sourceLanguage,
            'should_translate' => $computeTranslation
                ? ($parts['language'] !== strtolower($sourceLanguage) && ($this->config['translation_widget']['enabled'] ?? false))
                : false,
        ], array_filter($overrides, fn ($value) => $value !== null));

        return $data;
    }

    protected function normalizeLocale(string $locale): string
    {
        $locale = str_replace('_', '-', trim($locale));
        $parts = explode('-', $locale, 2);
        $language = strtolower($parts[0] ?? $this->config['default_language']);
        $region = strtoupper($parts[1] ?? $this->config['default_country']);

        return sprintf('%s-%s', $language, $region);
    }

    protected function isValidLocale(string $locale): bool
    {
        return (bool) preg_match('/^[a-z]{2}(?:[-_][A-Z]{2})?$/', $locale);
    }

    protected function isPublicIp(?string $ip): bool
    {
        if (!$ip) {
            return false;
        }

        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
