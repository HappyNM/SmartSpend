<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = "default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; script-src 'self' 'unsafe-inline'; font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
