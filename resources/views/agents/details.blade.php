@extends('layouts.app')

@section('content')
<div class="container mt-4">
    @include('layouts.newButton')

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="agentTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="deals-tab" data-bs-toggle="tab" href="#deals" role="tab" aria-controls="deals" aria-selected="false">Deals</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#activities" role="tab" aria-controls="activities" aria-selected="false">Diary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#storage" role="tab" aria-controls="storage" aria-selected="false">Storage</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="logs-tab" data-bs-toggle="tab" href="#logs" role="tab" aria-controls="logs" aria-selected="false">Logs</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="overflow-x-auto">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <th class="px-4 py-2 border">Reference</th>
                            <td class="px-4 py-2 border">{{ $agent->reference }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">First Name</th>
                            <td class="px-4 py-2 border">{{ $agent->first_name }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">Last Name</th>
                            <td class="px-4 py-2 border">{{ $agent->last_name }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">Email</th>
                            <td class="px-4 py-2 border">{{ $agent->email }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">Mobile</th>
                            <td class="px-4 py-2 border">{{ $agent->mobile }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">Phone</th>
                            <td class="px-4 py-2 border">{{ $agent->phone }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">Subscription Status</th>
                            <td class="px-4 py-2 border">{{ $agent->subscription_status }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">Labels</th>
                            <td class="px-4 py-2 border">
                                @if($agent->labels)
                                    @foreach(json_decode($agent->labels, true) as $label)
                                        <span class="badge bg-primary">{{ $label }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No labels</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border">Website</th>
                            <td class="px-4 py-2 border">
                                <a href="http://{{ $agent->website }}" target="_blank" class="text-blue-500 underline">{{ $agent->website }}</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Deals Tab -->
        <div class="tab-pane fade" id="deals" role="tabpanel" aria-labelledby="deals-tab">
            <p>Deals content goes here.</p>
        </div>

        <!-- Diary Tab -->
        <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
            <p>Diary content goes here.</p>
        </div>

        <!-- Storage Tab -->
        <div class="tab-pane fade" id="deals" role="tabpanel" aria-labelledby="deals-tab">
            <p>Storage content goes here.</p>
        </div>

        <!-- Logs Tab -->
        <div class="tab-pane fade" id="logs" role="tabpanel" aria-labelledby="logs-tab">
            <p>Logs content goes here.</p>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('agents.index') }}" class="btn btn-secondary">Back to List</a>
        <a href="{{ route('agents.edit', $agent->id) }}" class="btn btn-warning">Edit Agent</a>
    </div>
</div>
@endsection
