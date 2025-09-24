<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\AuditLog;

class LogFailedLogin
{
    public function handle(Failed $event)
    {
        AuditLog::create([
            'trace_id' => uniqid(),
            'type' => 'FAILED_LOGIN',
            'resource_action' => 'Failed login attempt',
            'user_name' => $event->user ? $event->user->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);
    }
}
