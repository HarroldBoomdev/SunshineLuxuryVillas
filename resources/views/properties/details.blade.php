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
    <!-- <li class="nav-item">
        <a class="nav-link" id="matches-tab" data-bs-toggle="tab" href="#matches" role="tab" aria-controls="matches" aria-selected="false">Matches</a>
    </li> -->
    <li class="nav-item">
        <a class="nav-link" id="logs-tab" data-bs-toggle="tab" href="#logs" role="tab" aria-controls="logs" aria-selected="false">Logs</a>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="propertyTabsContent">
    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
        <div>
            <h4>Property</h4>
        </div>
        <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Property Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Property Details</h5>
                    <table class="table table-striped">
                    <tr>
                        <th>Title</th>
                        <td>{{ $property->title ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Reference</th>
                        <td>{{ $property->reference ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Managing Agent</th>
                        <td>{{ $property->managing_agent ?? 'N/A' }}</td>
                    </tr>
                        <tr>
                        <th>Labels</th>
                            <td>
                                @php
                                    $labels = $property->labels;
                                    if (is_string($labels)) {
                                        $tmp = json_decode($labels, true);
                                        $labels = is_array($tmp) ? $tmp : array_filter(array_map('trim', explode(',', $labels)));
                                    } elseif (!is_array($labels)) {
                                        $labels = [];
                                    }
                                @endphp

                                @if(!empty($labels))
                                    @foreach($labels as $label)
                                        <span class="badge bg-primary">{{ $label }}</span>
                                    @endforeach
                                @endif

                            </td>
                        </tr>
                        <tr><th>Property Type</th><td>{{ $property->property_type }}</td></tr>
                        <tr><th>Status</th><td>{{ $property->status }}</td></tr>
                        <tr><th>Floor</th><td>{{ $property->floor }}</td></tr>
                        <tr><th>Floors</th><td>{{ $property->floors }}</td></tr>
                        <tr><th>Parking Spaces</th><td>{{ $property->parkingSpaces ?? 'N/A' }}</td></tr>
                        <tr><th>Living Rooms</th><td>{{ $property->living_rooms }}</td></tr>
                        <tr><th>Kitchens</th><td>{{ $property->kitchens }}</td></tr>
                        <tr><th>Bedrooms</th><td>{{ $property->bedrooms }}</td></tr>
                        <tr><th>Bathrooms</th><td>{{ $property->bathrooms }}</td></tr>
                        <tr><th>Showers</th><td>{{ $property->showers }}</td></tr>
                        <tr><th>Furnished</th><td>{{ $property->furnished }}</td></tr>
                        <tr><th>Structure</th><td>{{ $property->structure }}</td></tr>
                        <tr><th>Facade</th><td>{{ $property->facade }}</td></tr>
                        <tr><th>Frames Type</th><td>{{ $property->frames_type }}</td></tr>
                        <tr><th>Year of Construction</th><td>{{ $property->year_construction }}</td></tr>
                        <tr><th>Year of Renovation</th><td>{{ $property->year_renovation }}</td></tr>
                        <tr><th>Energy Efficiency Rating</th><td>{{ $property->energyEfficiency ?? 'N/A' }}</td></tr>
                        <tr><th>Communal Charge</th><td>{{ $property->communalCharge ?? 'N/A' }}</td></tr>
                        <tr><th>Communal Charge Frequency</th><td>{{ $property->comChargeFreq ?? 'N/A' }}</td></tr>
                        <tr><th>Reduced Price</th><td>{{ $property->reduced_price }}</td></tr>
                        <tr><th>Commission</th><td>{{ $property->commission }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
                <div class="row">
                    <!-- Large Preview Image -->
                    <div class="col-md-12">
                        <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                            <div class="carousel-inner">
                                <div class="row">
                                    @foreach($photos as $index => $photo)
                                        @php
                                            $thumbUrl = is_string($photo)
                                                ? trim($photo, ' "[]')
                                                : ($photo['url'] ?? $photo['src'] ?? $photo['path'] ?? $photo['image'] ?? $photo['photo_url'] ?? '');
                                        @endphp
                                        @if($thumbUrl)
                                            <div class="col-2">
                                                <img src="{{ $thumbUrl }}"
                                                    class="img-thumbnail img-fluid"
                                                    style="cursor: pointer; max-height: 100px; object-fit: cover;"
                                                    onclick="changeSlide({{ $index }})"
                                                    alt="Thumb {{ $index + 1 }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>



                            <!-- Carousel Controls -->
                            <a class="carousel-control-prev" href="#propertyCarousel" role="button" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </a>
                            <a class="carousel-control-next" href="#propertyCarousel" role="button" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>

                    <!-- Thumbnail Navigation -->
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            @foreach($photos as $index => $photo)
                                <div class="col-2">
                                    <img src="{{ trim($photo, ' "[]') }}"
                                        class="img-thumbnail img-fluid"
                                        style="cursor: pointer; max-height: 100px; object-fit: cover;"
                                        onclick="changeSlide({{ $index }})">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('properties.download-images', $property->id) }}" class="btn btn-primary">
                        Download All Images
                    </a>
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
                        <tr><th>Area</th><td>{{ $property->area }}</td></tr>
                        <tr><th>Country</th><td>{{ $property->country }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Land</h5>
                    <table class="table table-sm">
                    <tr><th>Registration number</th><td>{{ $property->regnum ?? 'N/A' }}</td></tr>
                    <tr><th>Section</th><td>{{ $property->section ?? 'N/A' }}</td></tr>
                    <tr><th>Plot number</th><td>{{ $property->plotnum ?? 'N/A' }}</td></tr>
                    <tr><th>Sheet/plan</th><td>{{ $property->sheetPlan ?? 'N/A' }}</td></tr>
                    <tr><th>Title deed</th><td>{{ $property->titleDead ?? 'N/A' }}</td></tr>
                    <tr><th>Share</th><td>{{ $property->share ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Areas</h5>
                    <table class="table table-sm">
                    <tr><th>Covered</th><td>{{ $property->covered ?? 'N/A' }}</td></tr>
                    <tr><th>Covered Veranda</th><td>{{ $property->coveredVeranda ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Distances</h5>

                    @php
                        // format numbers as X, X.5, X.25, etc., and append " km"
                        $fmtKm = function ($v) {
                            if ($v === null || $v === '') return 'N/A';
                            $n = (float) $v;
                            // trim trailing zeros
                            $clean = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
                            return $clean . ' km';
                        };
                    @endphp

                    <table class="table table-sm">
                    <tr><th>Amenities</th>        <td>{{ $fmtKm($property->amenities) }}</td></tr>
                    <tr><th>Airport</th>          <td>{{ $fmtKm($property->airport) }}</td></tr>
                    <tr><th>Sea</th>              <td>{{ $fmtKm($property->sea) }}</td></tr>
                    <tr><th>Public Transport</th> <td>{{ $fmtKm($property->publicTransport) }}</td></tr>
                    <tr><th>Schools</th>          <td>{{ $fmtKm($property->schools) }}</td></tr>
                    <tr><th>Resort</th>           <td>{{ $fmtKm($property->resort) }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Facilities</h5>

                    @php
                        $facilities = $property->facilities ?? [];
                        if (is_string($facilities)) {
                            $tmp = json_decode($facilities, true);
                            $facilities = is_array($tmp) ? $tmp : array_filter(array_map('trim', explode(',', $facilities)));
                        } elseif (!is_array($facilities)) {
                            $facilities = [];
                        }
                    @endphp

                    @if(empty($facilities))
                        <div class="text-muted">N/A</div>
                    @else
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($facilities as $f)
                                <span class="badge bg-secondary">{{ $f }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    </div>



    <div class="tab-pane fade" id="valuations" role="tabpanel" aria-labelledby="valuations-tab">
        <!-- Valuations Content -->
        <p>Valuations content goes here...</p>
    </div>
    <div class="tab-pane fade" id="insurance" role="tabpanel" aria-labelledby="insurance-tab">
        <!-- Insurance Content -->
        <p>Insurance content goes here...</p>
    </div>

    <!-- Deals Content -->
    <div class="tab-pane fade" id="deals" role="tabpanel" aria-labelledby="deals-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Deals</h4>
            <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createdealModal">+ New</button>
        </div>

            <table class="table table-bordered" id="dealsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Value</th>
                        <th>Pipeline</th>
                        <th>Stage</th>
                        <th>Branch</th>
                        <th>Assigned To</th>
                        <th>Expected Close Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deals as $deal)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $deal->title }}</td>
                            <td>{{ $deal->value }}</td>
                            <td>{{ $deal->pipeline }}</td>
                            <td>{{ $deal->stage }}</td>
                            <td>{{ $deal->branch }}</td>
                            <td>{{ $deal->assigned_to }}</td>
                            <td>{{ $deal->expected_close_date }}</td>
                            <td>
                            <button class="btn btn-sm btn-warning" onclick="editDeal({{ $deal->id }})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteDeal({{ $deal->id }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </div>

<!-- Create Deal -->
<div class="modal fade" id="createdealModal" tabindex="-1" aria-labelledby="createdealModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createdealModal">Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dealForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Deal Title">
                        </div>
                        <div class="col-md-6">
                            <label for="value" class="form-label">Value</label>
                            <input type="number" class="form-control" id="value" name="value" placeholder="€">
                        </div>
                        <div class="col-md-6">
                            <label for="pipeline" class="form-label">Pipeline</label>
                            <input type="text" class="form-control" id="pipeline" name="pipeline" placeholder="Pipeline">
                        </div>
                        <div class="col-md-6">
                            <label for="stage">Stage</label>
                                <select id="stage" name="stage" class="form-control">
                                <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="Interest">Interest</option>
                                    <option value="Follow up">Follow up</option>
                                    <option value="Viewing">Viewing</option>
                                    <option value="Negotiation">Negotiation</option>
                                    <option value="Sales Agreement">Sales Agreement</option>
                                    <option value="Land Reg">Land Reg</option>
                                </select>
                        </div>
                        <div class="col-md-6">
                            <label for="branch" class="form-label">Branch</label>
                            <input type="text" class="form-control" id="branch" name="branch" placeholder="Branch">
                        </div>
                        <div class="col-md-6">
                            <label for="assigned_to" class="form-label">Assigned To</label>
                            <input type="text" class="form-control" id="assigned_to" name="assigned_to" placeholder="Assigned To">
                        </div>
                        <div class="col-md-6">
                            <label for="expected_close_date" class="form-label">Expected Close Date</label>
                            <input type="date" class="form-control" id="expected_close_date" name="expected_close_date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitDealForm()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Deal -->
<div class="modal fade" id="editdealModal" tabindex="-1" aria-labelledby="editdealModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editdealModal">Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dealForm">
                    <div class="row g-3">
                    <input type="hidden" id="editDealId" name="dealId">
                        <div class="col-md-6">
                            <label for="edittitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edittitle" name="edittitle" placeholder="Deal Title">
                        </div>
                        <div class="col-md-6">
                            <label for="editvalue" class="form-label">Value</label>
                            <input type="number" class="form-control" id="editvalue" name="editvalue" placeholder="€">
                        </div>
                        <div class="col-md-6">
                            <label for="editpipeline" class="form-label">Pipeline</label>
                            <input type="text" class="form-control" id="editpipeline" name="editpipeline" placeholder="Pipeline">
                        </div>
                        <div class="col-md-6">
                            <label for="editstage">Stage</label>
                                <select id="editstage" name="editstage" class="form-control">
                                <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="Interest">Interest</option>
                                    <option value="Follow up">Follow up</option>
                                    <option value="Viewing">Viewing</option>
                                    <option value="Negotiation">Negotiation</option>
                                    <option value="Sales Agreement">Sales Agreement</option>
                                    <option value="Land Reg">Land Reg</option>
                                </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editbranch" class="form-label">Branch</label>
                            <input type="text" class="form-control" id="editbranch" name="editbranch" placeholder="Branch">
                        </div>
                        <div class="col-md-6">
                            <label for="editassigned_to" class="form-label">Assigned To</label>
                            <input type="text" class="form-control" id="editassigned_to" name="editassigned_to" placeholder="Assigned To">
                        </div>
                        <div class="col-md-6">
                            <label for="editexpected_close_date" class="form-label">Expected Close Date</label>
                            <input type="date" class="form-control" id="editexpected_close_date" name="editexpected_close_date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="updateDeal()">Save</button>
            </div>
        </div>
    </div>
</div>
    <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
        <!-- Activities Content -->
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Diaries</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleActivityModal">+ New</button>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Title</th>
                            <th>Resource</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Due Date</th>
                            <th>Duration</th>
                            <th>Assigned To</th>
                            <th>Notes</th>
                            <th>Client Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($diaries as $diary)
                            <tr>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>{{ $diary->type }}</td>
                                <td>{{ $diary->linked_to }}</td>
                                <td>{{ $diary->email ?? 'N/A' }}</td>
                                <td>{{ $diary->mobile ?? 'N/A' }}</td>
                                <td>{{ $diary->date }} {{ $diary->time }}</td>
                                <td>{{ $diary->duration }}</td>
                                <td>{{ $diary->assigned_to ?? 'N/A' }}</td>
                                <td>{{ $diary->notes }}</td>
                                <td>⭐⭐⭐⭐⭐</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                <!-- Schedule Activity Modal -->
                <div class="modal fade" id="scheduleActivityModal" tabindex="-1" aria-labelledby="scheduleActivityModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="scheduleActivityModalLabel">Schedule an Activity</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="diaryForm" action="{{ route('diaries.store') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <!-- Type Selection -->
                                        <div class="col-12 d-flex gap-2">
                                            <button type="button" class="btn btn-outline-secondary"><i class="fas fa-envelope"></i></button>
                                            <button type="button" class="btn btn-outline-secondary"><i class="fas fa-phone"></i></button>
                                            <button type="button" class="btn btn-outline-secondary"><i class="fas fa-calendar"></i></button>
                                            <button type="button" class="btn btn-outline-secondary"><i class="fas fa-user"></i></button>
                                            <button type="button" class="btn btn-outline-secondary"><i class="fas fa-tasks"></i></button>
                                        </div>

                                        <!-- Date, Time, and Duration -->
                                        <div class="col-md-4">
                                            <label for="date" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="time" class="form-label">Time</label>
                                            <input type="time" class="form-control" id="time" name="time" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="duration" class="form-label">Duration</label>
                                            <input type="text" class="form-control" id="duration" name="duration" placeholder="0h 15m">
                                        </div>

                                        <!-- Participants -->
                                        <div class="col-12">
                                            <label for="participants" class="form-label">Participants</label>
                                            <input type="text" class="form-control" id="participants" name="participants" placeholder="Enter participants">
                                        </div>

                                        <!-- Lead Source -->
                                        <div class="col-12">
                                            <label for="leadSource" class="form-label">Lead Source</label>
                                            <select id="leadSource" name="leadSource" class="form-control">
                                                <option value="">-</option>
                                                <option value="Referral">Referral</option>
                                                <option value="Online">Online</option>
                                            </select>
                                        </div>

                                        <!-- Notes -->
                                        <div class="col-12">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Enter notes"></textarea>
                                        </div>

                                        <!-- Linked To -->
                                        <div class="col-12">
                                            <label for="linkedTo" class="form-label">Linked To</label>
                                            <input type="text" id="linkedTo" name="linkedTo" class="form-control" placeholder="Search records">
                                        </div>

                                        <!-- Color Picker -->
                                        <div class="col-12">
                                            <label class="form-label">Color</label>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary" style="background-color: #ff0000;"></button>
                                                <button type="button" class="btn btn-outline-secondary" style="background-color: #00ff00;"></button>
                                                <button type="button" class="btn btn-outline-secondary" style="background-color: #0000ff;"></button>
                                                <button type="button" class="btn btn-outline-secondary" style="background-color: #ffff00;"></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="markAsDone">
                                    <label class="form-check-label" for="markAsDone">Mark as done</label>
                                </div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="storage" role="tabpanel" aria-labelledby="storage-tab">
        <!-- Storage Content -->
        <p>Storage content goes here...</p>
    </div>
    <div class="tab-pane fade" id="matches" role="tabpanel" aria-labelledby="matches-tab">
        <!-- Matches Content -->
        <p>Matches content goes here...</p>
    </div>
    <div class="tab-pane fade" id="logs" role="tabpanel" aria-labelledby="logs-tab">
        <!-- Logs Content -->
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Trace ID</th>
                        <th scope="col">Type</th>
                        <th scope="col">Action</th>
                        <th scope="col">User</th>
                        <th scope="col">Date & Time</th>
                        <th scope="col">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->trace_id }}</td>
                            <td>
                                <span class="badge
                                    @if($log->type === 'CREATE') bg-success
                                    @elseif($log->type === 'UPDATE') bg-warning text-dark
                                    @elseif($log->type === 'DELETE') bg-danger
                                    @elseif($log->type === 'VIEW') bg-info
                                    @else bg-secondary
                                    @endif
                                ">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td>{{ $log->resource_action }}</td>
                            <td>{{ $log->user_name }}</td>
                            <td>{{ $log->date_time->format('M d, Y H:i') }}</td>
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No logs found for this property.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>

<!-- Map Content -->
<div class="tab-content" id="propertyTabContent">
    <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
         <div id="propertyMap" style="height: 400px; width: 100%; border-radius: 8px;"></div>
    </div>
</div>



</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lat = {{ $property->latitude ?? 'null' }};
    const lng = {{ $property->longitude ?? 'null' }};
    const accuracy = "{{ $property->accuracy ?? 'pinpoint' }}";

    if (lat && lng) {
        const map = L.map('propertyMap').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        const marker = L.marker([lat, lng]).addTo(map);

        if (accuracy !== 'pinpoint') {
            const radius = parseInt(accuracy, 10);
            if (!isNaN(radius)) {
                L.circle([lat, lng], {
                    radius,
                    color: 'blue',
                    fillOpacity: 0.2
                }).addTo(map);
            }
        }
    } else {
        document.getElementById('propertyMap').innerHTML = '<div class="text-muted">No map location set for this property.</div>';
    }
});
</script>
@endpush

<script>

// Deals

function submitDealForm() {
    console.log('submitDealForm function executed');

    const form = document.getElementById('dealForm');
    const formData = new FormData(form);

    // Log form data for debugging
    for (const [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    // Submit the form data via fetch
    fetch('/deals', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createdealModal'));
                console.log('Modal instance:', modal);
                if (modal) modal.hide();

                // Clear the form fields
                form.reset();

                // Dynamically add the new deal to the table
                const newRow = `
                    <tr>
                        <td>#</td>
                        <td>${data.deal.title}</td>
                        <td>${data.deal.value}</td>
                        <td>${data.deal.pipeline}</td>
                        <td>${data.deal.stage}</td>
                        <td>${data.deal.branch}</td>
                        <td>${data.deal.assigned_to}</td>
                        <td>${data.deal.expected_close_date}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editDeal(${data.deal.id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteDeal(${data.deal.id})">Delete</button>
                        </td>
                    </tr>
                `;
                document.querySelector('#dealsTable tbody').insertAdjacentHTML('beforeend', newRow);

                // Alert success message
                alert('Deal created successfully!');
            } else {
                alert('Message: ' + (data.message || 'Failed to create deal.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again.');
        });
}

function editDeal(id) {
    fetch(`/deals/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const deal = data.deal;

                // Populate the modal fields with deal data
                document.getElementById('editDealId').value = deal.id;
                document.getElementById('edittitle').value = deal.title;
                document.getElementById('editvalue').value = deal.value;
                document.getElementById('editpipeline').value = deal.pipeline;
                document.getElementById('editstage').value = deal.stage;
                document.getElementById('editbranch').value = deal.branch;
                document.getElementById('editassigned_to').value = deal.assigned_to;
                document.getElementById('editexpected_close_date').value = deal.expected_close_date;

                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editdealModal'));
                editModal.show();
            } else {
                alert('Error: Unable to fetch deal details.');
            }
        })
        .catch(error => {
            console.error('Error fetching deal:', error);
            alert('An unexpected error occurred.');
        });
}

function updateDeal() {
    const dealId = document.getElementById('editDealId').value;

    const formData = {
        title: document.getElementById('edittitle').value,
        value: document.getElementById('editvalue').value,
        pipeline: document.getElementById('editpipeline').value,
        stage: document.getElementById('editstage').value,
        branch: document.getElementById('editbranch').value,
        assigned_to: document.getElementById('editassigned_to').value,
        expected_close_date: document.getElementById('editexpected_close_date').value,
    };

    fetch(`/deals/${dealId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify(formData),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Deal updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
}

function deleteDeal(id) {
    if (!confirm('Are you sure you want to delete this deal?')) {
        return;
    }

    fetch(`/deals/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Deal deleted successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
}


function changeSlide(index) {
        var carousel = new bootstrap.Carousel(document.getElementById('propertyCarousel'));
        carousel.to(index);
    }



</script>
@endsection
