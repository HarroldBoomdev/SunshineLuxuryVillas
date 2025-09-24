<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        // Debug log
        // Log::info('Logout Event Triggered', [
        //     'user' => $event->user,
        //     'ip' => request()->ip(),
        // ]);

        // try {
        //     AuditLog::create([
        //         'trace_id' => uniqid(),
        //         'type' => 'LOGOUT',
        //         'resource_action' => 'User logged out',
        //         'user_name' => $event->user->name ?? 'Guest', // Handle null user
        //         'ip_address' => request()->ip(),
        //         'date_time' => now(),
        //     ]);

        //     Log::info('Logout event logged successfully.');
        // } catch (\Exception $e) {
        //     Log::error('Error logging logout event', [
        //         'message' => $e->getMessage(),
        //         'trace' => $e->getTraceAsString(),
        //     ]);
        // }
    }
}
