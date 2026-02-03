<?php

namespace App\Http\Middleware;

use App\Services\LocaleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocalePrefix
{
    protected LocaleManager $manager;

    public function __construct(LocaleManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $path = ltrim($request->path(), '/');

        if ($this->shouldSkip($request, $path)) {
            return $next($request);
        }

        // If path already begins with a locale segment, strip it for routing.
        $prefixedLocale = $this->getLocalePrefix($path);
        if ($prefixedLocale) {
            // Persist chosen locale (so DetectLocale will apply it)
            if ($request->cookie('site_locale') !== $prefixedLocale) {
                cookie()->queue(cookie('site_locale', $prefixedLocale, 60 * 24 * 30));
            }
            return $next($request);
        }

        // Determine preferred locale: query/cookie/header -> domain override -> GeoIP.
        $forcedLocale = $request->query('lang')
            ?? $request->cookie('site_locale')
            ?? $request->header('X-User-Locale');

        $domainLocale = $this->extractDomainLocale($request);
        $preferredLocale = $forcedLocale ?? $domainLocale;

        $localeData = $this->manager->resolve($request->ip(), $preferredLocale);
        $localeSlug = strtolower(str_replace('_', '-', $localeData['locale'] ?? 'en-us'));

        $newPath = $localeSlug;
        if ($path !== '' && $path !== '/') {
            $newPath .= '/' . $path;
        }

        $targetUrl = $request->getSchemeAndHttpHost() . '/' . ltrim($newPath, '/');
        if ($request->getQueryString()) {
            $targetUrl .= '?' . $request->getQueryString();
        }

        return redirect()->to($targetUrl, 302);
    }

    protected function shouldSkip(Request $request, string $path): bool
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
            return true;
        }

        if ($path === '' || $path === '/') {
            return false;
        }

        // Static assets / internal endpoints
        if (
            str_starts_with($path, 'api') ||
            str_starts_with($path, 'storage') ||
            str_starts_with($path, 'vendor') ||
            str_starts_with($path, 'assets') ||
            str_starts_with($path, 'build') ||
            str_starts_with($path, 'css') ||
            str_starts_with($path, 'js') ||
            str_starts_with($path, 'images') ||
            str_starts_with($path, 'frontend') ||
            str_starts_with($path, 'sitemap') ||
            str_starts_with($path, 'robots')
        ) {
            return true;
        }

        if (str_ends_with(strtolower($path), '.xml')) {
            return true;
        }

        return false;
    }

    protected function hasLocalePrefix(string $path): bool
    {
        return (bool) $this->getLocalePrefix($path);
    }

    protected function getLocalePrefix(string $path): ?string
    {
        $first = strtolower(explode('/', $path, 2)[0] ?? '');
        if (!preg_match('/^[a-z]{2}-[a-z]{2}$/', $first)) {
            return null;
        }

        return $first;
    }

    protected function stripLocalePrefix(string $path): string
    {
        $parts = explode('/', $path, 2);
        return $parts[1] ?? '';
    }

    protected function extractDomainLocale(Request $request): ?string
    {
        $host = $request->getHost();
        $mapping = config('locale.domain_locale_map', []);

        foreach ($mapping as $pattern => $locale) {
            if (fnmatch($pattern, $host)) {
                return $locale;
            }
        }

        return null;
    }
}
