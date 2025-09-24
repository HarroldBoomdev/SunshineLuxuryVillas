<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'User is not authenticated.');
        }

        if (!$user->hasRole('Admin')) {
            logger()->info("User authenticated but does not have 'Admin' role", [
                'user_id' => $user->id,
                'roles' => $user->getRoleNames()
            ]);
            abort(403, 'User does not have the Admin role.');
        }

        return $next($request);
    }
}
