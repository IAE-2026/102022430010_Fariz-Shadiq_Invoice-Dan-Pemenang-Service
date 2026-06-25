<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force request to expect JSON
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        // Force response Content-Type header to be application/json
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
