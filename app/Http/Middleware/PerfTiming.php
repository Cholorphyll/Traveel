<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerfTiming
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        $durationMs = (microtime(true) - $start) * 1000.0;

        // Add Server-Timing header (consumable in browser DevTools)
        // See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Server-Timing
        $existing = $response->headers->get('Server-Timing');
        $metric = 'app;desc="Laravel app";dur=' . number_format($durationMs, 2, '.', '');
        $response->headers->set('Server-Timing', trim(($existing ? ($existing . ', ') : '') . $metric));

        // Also expose duration via a custom header for quick inspection
        $response->headers->set('X-App-Duration', number_format($durationMs, 2, '.', '') . 'ms');

        // Log basic request timing
        try {
            Log::info('[PERF] Request', [
                'method' => $request->getMethod(),
                'path'   => $request->getPathInfo(),
                'status' => $response->getStatusCode(),
                'dur_ms' => (float) number_format($durationMs, 2, '.', ''),
                'ip'     => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            // no-op
        }

        return $response;
    }
}
