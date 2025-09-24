<?php

namespace App\Observers;

use App\Models\AccessLog;

class AccessLogObserver
{
    public function created($model)
    {
        // Prevent logging recursive actions for AccessLog itself
        if ($model instanceof AccessLog) {
            return;
        }

        AccessLog::create([
            'trace_id' => uniqid(),
            'type' => 'CREATE',
            'url' => request()->fullUrl(), // Provide the full URL
            'resource' => class_basename($model) . ' - Created',
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);
    }

    public function updated($model)
    {
        // Prevent logging recursive actions for AccessLog itself
        if ($model instanceof AccessLog) {
            return;
        }

        AccessLog::create([
            'trace_id' => uniqid(),
            'type' => 'UPDATE',
            'url' => request()->fullUrl(), // Provide the full URL
            'resource' => class_basename($model) . ' - Updated',
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);
    }

    public function deleted($model)
    {
        // Prevent logging recursive actions for AccessLog itself
        if ($model instanceof AccessLog) {
            return;
        }

        AccessLog::create([
            'trace_id' => uniqid(),
            'type' => 'DELETE',
            'url' => request()->fullUrl(), // Provide the full URL
            'resource' => class_basename($model) . ' - Deleted',
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);
    }
}
