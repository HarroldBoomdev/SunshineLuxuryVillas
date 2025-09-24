<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\AuditLog;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        // AuditLog::create([
        //     'trace_id' => uniqid(),
        //     'type' => 'LOGIN',
        //     'resource_action' => 'User logged in',
        //     'user_name' => $event->user->name,
        //     'ip_address' => request()->ip(),
        //     'date_time' => now(),
        // ]);
    }
}
