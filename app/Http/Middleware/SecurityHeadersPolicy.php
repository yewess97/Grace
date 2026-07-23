<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SecurityHeadersPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Closure|JsonResponse|Response|RedirectResponse
     * @throws BindingResolutionException
     */
    final public function handle(Request $request, Closure $next): Closure|JsonResponse|Response|RedirectResponse
    {
        $response = $next($request);

        // Generate a unique nonce for inline scripts and styles to enhance security
        $nonce = app()->make('csp_nonce');

        $style_hashes = "'sha256-47DEQpj8HBSa+/TImW+5JCeuQeRkm5NMpJWZG3hSuFU=' 'sha256-3ITP0qhJJYBulKb1omgiT3qOK6k0iB3rMDhGfpM8b7c='";

        $style_src_elem = "style-src-elem 'self' fonts.googleapis.com cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com www.gstatic.com 'nonce-$nonce' $style_hashes";
        $style_src_attr = "style-src-attr 'unsafe-inline'";
        $script_src  = "script-src 'self' cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com www.gstatic.com cdn.tiny.cloud 'nonce-$nonce'";
        $font_src    = "font-src 'self' fonts.googleapis.com cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com fonts.gstatic.com data:";
        $connect_src = "connect-src 'self' www.gstatic.com countries.dev api.emailjs.com cdnjs.cloudflare.com unpkg.com";
        $img_src     = "img-src 'self' blob: data: cdn.jsdelivr.net unpkg.com flagcdn.com upload.wikimedia.org img.shields.io";

        $bfcache_public_routes = [HOME, PRODUCTS_LIST, PAYMENT, ABOUT_US, CONTACT_US, DOCUMENTATION];

        $response->headers->set('Content-Security-Policy', "default-src 'self'; frame-ancestors 'self'; $style_src_elem; $style_src_attr; $script_src; $font_src; $connect_src; $img_src;");

        // X-Frame-Options - Prevents Clickjacking attacks by blocking iframes
        $response->headers->set('X-Frame-Options', 'DENY');

        // Strict-Transport-Security (HSTS) - Forces the use of HTTPS for security
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Cross-Origin-Resource-Policy - Restricts which origins can load resources from this site
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // Cross-Origin-Opener-Policy - Isolates the browsing context to prevent cross-origin popups from interacting with this window
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // Cross-Origin-Embedder-Policy (require-corp) - Prevents embedding the website in foreign origins
        // $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Cross-Origin-Embedder-Policy (credentialless) - Protects against cross-origin attacks without breaking third-party resources (CDNs, iframes, widgets)
        $response->headers->set('Cross-Origin-Embedder-Policy', 'credentialless');

        // X-Content-Type-Options - Prevents browsers from MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Permissions-Policy - Restricts access to browser features (e.g., camera, microphone, geolocation)
        $response->headers->set('Permissions-Policy', "geolocation=(), microphone=(), camera=(), payment=()");

        // Referrer-Policy - Controls how much referrer information is sent with requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // X-XSS-Protection - Adds XSS protection for older browsers (though modern browsers ignore it)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Cache-Control
        $request->routeIs($bfcache_public_routes) || $request->is('/')
            // Safe headers for public pages (Allows instant back/forward cache)
            ? $response->headers->set('Cache-Control', 'no-cache, private, must-revalidate')
            // Strict headers for private/sensitive pages (Prevents storing sensitive data in browser cache)
            : $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return $response;
    }
}
