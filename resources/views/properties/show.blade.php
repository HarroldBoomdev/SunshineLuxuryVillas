@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>#{{ $property->id }} Property</h4>
        </div>
        <div>
            <button class="btn btn-success dropdown-toggle me-2" type="button" id="actionsMenu" data-bs-toggle="dropdown" aria-expanded="false">
                Actions Menu
            </button>
            <ul class="dropdown-menu" aria-labelledby="actionsMenu">
                <li><a class="dropdown-item" href="#">Edit</a></li>
                <li><a class="dropdown-item" href="#">Delete</a></li>
                <li><a class="dropdown-item" href="#">Archive</a></li>
            </ul>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="#">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Map</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Tenants</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Valuations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Insurance</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Deals</a>
        </li>
    </ul>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Property Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Property Details</h5>
                    <table class="table table-striped">
                        <tr>
                            <th>Category</th>
                            <td>{{ $property->category }}</td>
                        </tr>
                        <tr>
                            <th>Reference</th>
                            <td>{{ $property->reference }}</td>
                        </tr>
                        <tr>
                            <th>Branch</th>
                            <td>{{ $property->branch }}</td>
                        </tr>
                        <tr>
                            <th>Managing Agent</th>
                            <td>{{ $property->managing_agent }}</td>
                        </tr>
                        <tr>
                            <th>Labels</th>
                            <td>
                                @foreach ($property->labels as $label)
                                    <span class="badge bg-primary">{{ $label }}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>{{ $property->type }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $property->status }}</td>
                        </tr>
                        <tr>
                            <th>Floors</th>
                            <td>{{ $property->floors }}</td>
                        </tr>
                        <tr>
                            <th>Year of Construction</th>
                            <td>{{ $property->year_construction }}</td>
                        </tr>
                        <tr>
                            <th>Year of Renovation</th>
                            <td>{{ $property->year_renovation }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Property Image -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="{{ $property->photos }}" class="img-fluid" alt="Property Image" onerror="this.src='{{ asset('images/default.jpg') }}'">
                    </div>
                </div>


            <!-- Owner Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Owner</h5>
                    <p>{{ $property->owner }}</p>
                </div>
            </div>

            <!-- Location Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Location</h5>
                    <table class="table table-sm">
                        <tr>
                            <th>Area</th>
                            <td>{{ $property->area }}</td>
                        </tr>
                        <tr>
                            <th>Country</th>
                            <td>{{ $property->country }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
