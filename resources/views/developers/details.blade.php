@extends('layouts.app')

@section('content')
<div class="container mt-4">
    @include('layouts.newButton')

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="propertyTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="map-tab" data-bs-toggle="tab" href="#map" role="tab" aria-controls="map" aria-selected="false">Map</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="deals-tab" data-bs-toggle="tab" href="#deals" role="tab" aria-controls="deals" aria-selected="false">Deals</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#activities" role="tab" aria-controls="activities" aria-selected="false">Diary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="storage-tab" data-bs-toggle="tab" href="#storage" role="tab" aria-controls="storage" aria-selected="false">Storage</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="logs-tab" data-bs-toggle="tab" href="#logs" role="tab" aria-controls="logs" aria-selected="false">Logs</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="card">
                <div class="card-header">
                    All Developers
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" class="col-sm-3">Reference</th>
                                <td class="col-sm-9">{{ $developer->reference }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="col-sm-3">Name</th>
                                <td class="col-sm-9">{{ $developer->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="col-sm-3">Email</th>
                                <td class="col-sm-9">{{ $developer->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="col-sm-3">Phone</th>
                                <td class="col-sm-9">{{ $developer->phone }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="col-sm-3">Website</th>
                                <td class="col-sm-9">
                                    <a href="http://{{ $developer->website }}" target="_blank">{{ $developer->website }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Other tabs (Map, Deals, Diary, etc.) -->
        <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
            <p>Map content goes here.</p>
        </div>
        <div class="tab-pane fade" id="deals" role="tabpanel" aria-labelledby="deals-tab">
            <p>Deals content goes here.</p>
        </div>
        <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
            <p>Diary content goes here.</p>
        </div>
        <div class="tab-pane fade" id="storage" role="tabpanel" aria-labelledby="storage-tab">
            <p>Storage content goes here.</p>
        </div>
        <div class="tab-pane fade" id="logs" role="tabpanel" aria-labelledby="logs-tab">
            <p>Logs content goes here.</p>
        </div>
    </div>
</div>
@endsection
