@extends('layouts.app')

@section('content')
<div class="container mt-4">
@include('layouts.newButton')
    <h1>Audit Log</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="px-4 py-2 border">Trace ID</th>
                <th class="px-4 py-2 border">Type</th>
                <th class="px-4 py-2 border">Resource/Action</th>
                <th class="px-4 py-2 border">User</th>
                <th class="px-4 py-2 border">Date & Time</th>
                <th class="px-4 py-2 border">IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border">{{ $log->trace_id }}</td>
                    <td class="px-4 py-2 border">
                        <span class="inline-block px-2 py-1 rounded text-white 
                            {{ $log->type == 'GET' ? 'bg-blue-500' : 'bg-red-500' }}">
                            {{ $log->type }}
                        </span>
                    </td>
                    <td class="px-4 py-2 border">{{ $log->resource_action }}</td>
                    <td class="px-4 py-2 border">{{ $log->user_name }}</td>
                    <td class="px-4 py-2 border">{{ $log->date_time }}</td>
                    <td class="px-4 py-2 border">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No logs found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection
