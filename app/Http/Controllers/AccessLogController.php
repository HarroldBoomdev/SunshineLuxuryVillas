<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use Illuminate\Http\Request;

class AccessLogController extends Controller
{
    public function index()
    {
        // Log the page view
        AccessLog::create([
            'trace_id' => uniqid(),
            'type' => 'VIEW',
            'url' => request()->fullUrl(), // Log the full URL
            'resource_action' => 'Access Logs Page',
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest',
            'ip_address' => request()->ip(),
            'date_time' => now(),
        ]);

        // Retrieve logs
        $logs = AccessLog::latest()->paginate(10); // Adjust pagination as needed

        // Pass logs to the view
        return view('access.index', compact('logs'));
    }



    // Show: Display a single log entry
    public function show($id)
    {
        $log = AccessLog::findOrFail($id);
        return view('access.show', compact('log'));
    }

    // Store: Save a new log entry
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trace_id' => 'required|unique:access_logs,trace_id',
            'type' => 'required',
            'url' => 'required',
            'resource' => 'nullable',
            'user_name' => 'required',
            'user_image' => 'nullable',
            'ip_address' => 'required|ip',
            'operating_system' => 'nullable',
            'date_time' => 'required|date',
        ]);

        AccessLog::create($validated);
        return redirect()->route('access-logs.index')->with('success', 'Access log created successfully.');
    }
}
