<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Random\RandomException;

class SecurityHeadersPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Closure|Response|RedirectResponse|JsonResponse
     * @throws RandomException
     */
    final public function handle(Request $request, Closure $next): Closure|Response|RedirectResponse|JsonResponse
    {
        $response = $next($request);

        // Generate a unique nonce for inline scripts and styles to enhance security
        $nonce = base64_encode(random_bytes(16));

        // Content Security Policy (CSP) - Prevents XSS, data injection, and unauthorized resource loading
        $response->header('Content-Security-Policy',
            "default-src 'self';
            script-src 'self' fonts.gstatic.com cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com 'nonce-{$nonce}';
            style-src 'self' fonts.googleapis.com cdnjs.cloudflare.com unpkg.com 'nonce-{$nonce}';
            font-src 'self' fonts.googleapis.com;
            img-src 'self' data:;
            frame-ancestors 'self';"
        );

        // X-Content-Type-Options - Prevents browsers from MIME-type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Referrer-Policy - Controls how much referrer information is sent with requests
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy - Restricts access to browser features (e.g., camera, microphone, geolocation)
        $response->header('Permissions-Policy', "geolocation=(), microphone=(), camera=(), payment=()");

        // Cross-Origin-Resource-Policy - Restricts which origins can load resources from this site
        $response->header('Cross-Origin-Resource-Policy', 'same-origin');

        // Cross-Origin-Embedder-Policy - Prevents embedding the website in foreign origins
        $response->header('Cross-Origin-Embedder-Policy', 'require-corp');

        // Strict-Transport-Security (HSTS) - Forces the use of HTTPS for security
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // X-Frame-Options - Prevents Clickjacking attacks by blocking iframes
        $response->header('X-Frame-Options', 'DENY');

        // X-XSS-Protection - Adds XSS protection for older browsers (though modern browsers ignore it)
        $response->header('X-XSS-Protection', '1; mode=block');

        // Cache-Control - Prevents storing sensitive data in browser cache
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return $response;
    }
}
