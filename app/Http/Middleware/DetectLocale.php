<?php

namespace App\Http\Middleware;

use App\Services\LocaleManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DetectLocale
{
    protected LocaleManager $manager;

    public function __construct(LocaleManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $forcedLocale = $this->extractPreferredLocale($request);
        $domainLocale = $this->extractDomainLocale($request);
        $preferredLocale = $forcedLocale ?? $domainLocale;
        $localeData = $this->manager->resolve($request->ip(), $preferredLocale);

        app()->setLocale($localeData['language'] ?? config('locale.default_language'));
        app()->instance('detectedLocale', $localeData);
        View::share('detectedLocale', $localeData);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        if ($forcedLocale && $forcedLocale !== $request->cookie('site_locale')) {
            $response->headers->setCookie(
                cookie('site_locale', $localeData['locale'], 60 * 24 * 30)
            );
        }

        if ($this->shouldMutateHtml($response)) {
            $markup = $response->getContent();
            $markup = $this->applyHtmlLangAttribute($markup, $localeData);
            $markup = $this->maybeInjectTranslationWidget($markup, $localeData);
            $response->setContent($markup);
        }

        return $response;
    }

    protected function extractPreferredLocale(Request $request): ?string
    {
        return $request->query('lang')
            ?? $request->cookie('site_locale')
            ?? $request->header('X-User-Locale');
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

    protected function shouldMutateHtml(Response $response): bool
    {
        if (!$response instanceof Response) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type');
        return $contentType && str_starts_with(strtolower($contentType), 'text/html');
    }

    protected function applyHtmlLangAttribute(string $markup, array $localeData): string
    {
        $locale = $localeData['locale'] ?? config('locale.default_locale', 'en-US');
        $langAttribute = sprintf('lang="%s"', e($locale));

        if (preg_match('/<html[^>]+lang=/i', $markup)) {
            $replaced = preg_replace(
                '/(<html[^>]*\blang=)(["\'])(.*?)(\2)/i',
                '$1$2' . e($locale) . '$2',
                $markup,
                1
            );

            return $replaced !== null ? $replaced : $markup;
        }

        $added = preg_replace('/<html(?![^>]*\blang=)/i', '<html ' . $langAttribute, $markup, 1);
        return $added !== null ? $added : $markup;
    }

    protected function maybeInjectTranslationWidget(string $markup, array $localeData): string
    {
        $config = config('locale.translation_widget');
        if (empty($config['enabled']) || empty($localeData['should_translate'])) {
            return $markup;
        }

        $targetLang = $localeData['language'];
        $sourceLang = $localeData['source_language'] ?? $config['source_language'] ?? 'en';

        $widget = <<<HTML
<script>
    window.localeBootstrapper = window.localeBootstrapper || {};
    window.localeBootstrapper.targetLanguage = "{$targetLang}";
    window.localeBootstrapper.sourceLanguage = "{$sourceLang}";
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: window.localeBootstrapper.sourceLanguage,
            includedLanguages: window.localeBootstrapper.targetLanguage,
            autoDisplay: true
        }, 'google_translate_container');
    }
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" defer></script>
HTML;

        if (!str_contains($markup, 'google_translate_container')) {
            $markup = preg_replace(
                '/<body([^>]*)>/i',
                '<body$1><div id="google_translate_container" class="visually-hidden"></div>',
                $markup,
                1
            );
        }

        if (str_contains($markup, '</head>')) {
            return str_replace('</head>', $widget . PHP_EOL . '</head>', $markup);
        }

        return $markup . $widget;
    }
}
