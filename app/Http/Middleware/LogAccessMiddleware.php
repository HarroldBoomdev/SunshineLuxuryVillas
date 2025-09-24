<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AccessLog; 
use App\Models\User;

class LogAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // dd(auth()->check(), auth()->user());

        $response = $next($request);

        AccessLog::create([
            'trace_id' => uniqid(), 
            'type' => $request->method(), 
            'url' => $request->fullUrl(), 
            'resource' => $request->path(), 
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest', 
            'user_image' => auth()->check() ? auth()->user()->profile_image ?? null : null, 
            'ip_address' => $request->ip(), 
            'operating_system' => $this->getOperatingSystem($request->header('User-Agent')), 
            'date_time' => now(), 
        ]);

        return $response;
    }

    private function getOperatingSystem(?string $userAgent)
    {
        if (!$userAgent) {
            return null;
        }

        if (preg_match('/Windows NT/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            return 'Mac OS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'iOS';
        }

        return 'Unknown';
    }
}
