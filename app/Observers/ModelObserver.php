<?php

namespace App\Observers;

use App\Models\AuditLog;

class ModelObserver
{
    public function created($model)
    {
        AuditLog::create([
            'trace_id' => uniqid(),
            'type' => 'CREATE',
            'resource_action' => class_basename($model) . ' - Created',
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);
    }

    public function updated($model)
    {
        AuditLog::create([
            'trace_id' => uniqid(),
            'type' => 'UPDATE',
            'resource_action' => class_basename($model) . ' - Updated',
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);
    }

    public function deleted($model)
    {
        AuditLog::create([
            'trace_id' => uniqid(),
            'type' => 'DELETE',
            'resource_action' => class_basename($model) . ' - Deleted',
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);
    }
}
