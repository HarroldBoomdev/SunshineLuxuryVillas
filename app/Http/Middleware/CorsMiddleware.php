<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    // Add every frontend origin that should be allowed
    private array $allowedOrigins = [
        'https://www.sunshineluxuryvillas.co.uk',
        'https://sunshineluxuryvillas.co.uk',
        'http://127.0.0.1:8080',
        'http://localhost:8080',
    ];

    private string $allowedHeaders = 'Origin, Content-Type, Accept, Authorization, X-Requested-With';

    // Methods you want to allow
    private string $allowedMethods = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';

    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('Origin');
        $allowOrigin = $this->originIsAllowed($origin) ? $origin : null;

        // Preflight: answer immediately
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsResponse(new Response('', 204), $allowOrigin);
        }

        $response = $next($request);
        return $this->corsResponse($response, $allowOrigin);
    }

    private function corsResponse(Response $response, ?string $allowOrigin): Response
    {
        // Only set CORS headers if the origin is allowed
        if ($allowOrigin) {
            $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
            $response->headers->set('Access-Control-Allow-Methods', $this->allowedMethods);
            $response->headers->set('Access-Control-Allow-Headers', $this->allowedHeaders);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            // Make the response cache vary by Origin so CDNs/proxies don’t mix it up
            $response->headers->set('Vary', 'Origin');
        }

        return $response;
    }

    private function originIsAllowed(?string $origin): bool
    {
        if (!$origin) return false;
        return in_array($origin, $this->allowedOrigins, true);
    }
}
