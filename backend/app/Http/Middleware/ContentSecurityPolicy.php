<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = "default-src 'self'; "
             . "script-src 'self' https://cdn.tailwindcss.com https://unpkg.com; "
             . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
             . "font-src https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
             . "img-src 'self' data:; "
             . "object-src 'none'; "
             . "base-uri 'self'; "
             . "form-action 'self'; "
             . "frame-ancestors 'none'; "
             . "upgrade-insecure-requests;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
