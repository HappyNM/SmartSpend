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

        $csp = "default-src 'self'; base-uri 'self'; frame-ancestors 'self'; style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; style-src-elem 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; script-src 'self' 'unsafe-inline' 'unsafe-eval'; script-src-elem 'self' 'unsafe-inline' 'unsafe-eval'; font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com data:; img-src 'self' data: https:; connect-src 'self' https:;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
