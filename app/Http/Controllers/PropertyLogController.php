<?php

namespace App\Http\Controllers;

use App\Models\PropertyActivityLog;
use Illuminate\Http\Request;

class PropertyLogController extends Controller
{
    public function tab()
    {
        $logs = PropertyActivityLog::with(['property:id,reference,title', 'user:id,name'])
            ->latest()
            ->paginate(50);

        return view('properties.partials.logs-table', compact('logs'));
    }
}
