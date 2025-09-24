@extends('layouts.app')

@section('content')
<div class="container mt-4">
@include('layouts.newButton')
    <h1>Access Log</h1>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 border">Trace ID</th>
                <th class="px-4 py-2 border">Request Method</th>
                <th class="px-4 py-2 border">URL</th>
                <th class="px-4 py-2 border">Resource</th>
                <th class="px-4 py-2 border">User</th>
                <th class="px-4 py-2 border">IP Address</th>
                <th class="px-4 py-2 border">Operating System</th>
                <th class="px-4 py-2 border">Date & Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border">{{ $log->trace_id }}</td>
                    <td class="px-4 py-2 border">
                        <span class="inline-block px-2 py-1 rounded text-white {{ $log->type == 'GET' ? 'bg-blue-500' : 'bg-red-500' }}">
                            {{ $log->type }}
                        </span>
                    </td>
                    <td class="px-4 py-2 border">{{ $log->url }}</td>
                    <td class="px-4 py-2 border">{{ $log->resource }}</td>
                    <td class="px-4 py-2 border flex items-center">
                        <img src="{{ $log->user_image }}" alt="User Image" class="h-8 w-8 rounded-full mr-2">
                        {{ $log->user_name }}
                    </td>
                    <td class="px-4 py-2 border">{{ $log->ip_address }}</td>
                    <td class="px-4 py-2 border">{{ $log->operating_system }}</td>
                    <td class="px-4 py-2 border">{{ $log->date_time }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">No logs found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection
