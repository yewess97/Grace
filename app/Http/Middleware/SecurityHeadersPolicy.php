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

        $style_hashes = "'sha256-3ITP0qhJJYBulKb1omgiT3qOK6k0iB3rMDhGfpM8b7c=' 'sha256-47DEQpj8HBSa+/TImW+5JCeuQeRkm5NMpJWZG3hSuFU=' 'sha256-ORYTfWgGeaDP2b2S7MVkXsd+c7Cui4ZVcoC+HzzP/nM=' 'sha256-rRl7CHm+5M/6W322VSJ6spPUB/xQLLTchIPOFgti90E=' 'sha256-ucCfCRKeUNEuZJWr2Xeo7Jf7mdikyR4eZ280hmS9Yk0=' 'sha256-0R/LX01gwFNdds3kDBfH0kPtAnQmJw3/a8ABXs9E1Lc=' 'sha256-DgScPVb2NUID/mdJI+9ya5jZBHp+wZTUP02zAHkqfX4=' 'sha256-DqcpTn66pq93Nnly0Vdus1d84FxtJJURyLvD0Yb0JJM=' 'sha256-2+dS+n9Pah47gYjmchfaYD5g/iEbiyoAg7SGmiJtn0Y=' 'unsafe-hashes'";

        $style_src   = "style-src 'self' fonts.googleapis.com cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com www.gstatic.com 'nonce-$nonce' $style_hashes";
        $script_src  = "script-src 'self' cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com www.gstatic.com cdn.tiny.cloud 'nonce-$nonce'";
        $font_src    = "font-src 'self' fonts.googleapis.com cdnjs.cloudflare.com unpkg.com fonts.gstatic.com data:";
        $connect_src = "connect-src 'self' www.gstatic.com www.apicountries.com api.emailjs.com unpkg.com";
        $img_src     = "img-src 'self' data: cdn.jsdelivr.net flagcdn.com upload.wikimedia.org";

        $response->headers->set('Content-Security-Policy', "default-src 'self'; frame-ancestors 'self'; $style_src; $script_src; $font_src; $connect_src; $img_src;");

        // X-Content-Type-Options - Prevents browsers from MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer-Policy - Controls how much referrer information is sent with requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy - Restricts access to browser features (e.g., camera, microphone, geolocation)
        $response->headers->set('Permissions-Policy', "geolocation=(), microphone=(), camera=(), payment=()");

        // Cross-Origin-Resource-Policy - Restricts which origins can load resources from this site
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // Cross-Origin-Embedder-Policy (require-corp) - Prevents embedding the website in foreign origins
        // $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Cross-Origin-Embedder-Policy (credentialless) - Protects against cross-origin attacks without breaking third-party resources (CDNs, iframes, widgets)
        $response->headers->set('Cross-Origin-Embedder-Policy', 'credentialless');

        // Strict-Transport-Security (HSTS) - Forces the use of HTTPS for security
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // X-Frame-Options - Prevents Clickjacking attacks by blocking iframes
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-XSS-Protection - Adds XSS protection for older browsers (though modern browsers ignore it)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Cache-Control - Prevents storing sensitive data in browser cache
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return $response;
    }
}
