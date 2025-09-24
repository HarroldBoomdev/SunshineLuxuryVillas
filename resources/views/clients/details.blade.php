@extends('layouts.app')

@section('content')
<div class="container mt-4">
@include('layouts.newButton')
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="clientTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="deals-tab" data-bs-toggle="tab" href="#deals" role="tab" aria-controls="deals" aria-selected="false">Deals</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#activities" role="tab" aria-controls="activities" aria-selected="false">Activities</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="storage-tab" data-bs-toggle="tab" href="#storage" role="tab" aria-controls="storage" aria-selected="false">Storage</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="matches-tab" data-bs-toggle="tab" href="#matches" role="tab" aria-controls="matches" aria-selected="false">Matches</a>
        </li>





        <li class="nav-item">
            <a class="nav-link" id="logs-tab" data-bs-toggle="tab" href="#logs" role="tab" aria-controls="logs" aria-selected="false">Logs</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="clientTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Client Details</div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr><th>Reference</th><td>{{ $client->id }}</td></tr>
                                    <tr><th>First Name</th><td>{{ $client->first_name }}</td></tr>
                                    <tr><th>Last Name</th><td>{{ $client->last_name }}</td></tr>
                                    <tr><th>Address</th><td>{{ $client->address }}</td></tr>
                                    <tr><th>Zipcode</th><td>{{ $client->zipcode }}</td></tr>
                                    <tr><th>City</th><td>{{ $client->city }}</td></tr>
                                    <tr><th>Region</th><td>{{ $client->region }}</td></tr>
                                    <tr><th>Country</th><td>{{ $client->country }}</td></tr>
                                    <tr><th>Phone</th><td>{{ $client->phone }}</td></tr>
                                    <tr><th>Mobile</th><td>{{ $client->mobile }}</td></tr>
                                    <tr><th>Email</th><td>{{ $client->email }}</td></tr>
                                    <tr><th>Nationality</th><td>{{ $client->nationality }}</td></tr>
                                    <tr><th>ID Card Number</th><td>{{ $client->id_card_number }}</td></tr>
                                    <tr><th>Passport Number</th><td>{{ $client->passport_number }}</td></tr>
                                    <tr><th>Labels</th><td>{{ $client->labels ?? 'No labels' }}</td></tr>
                                    <tr><th>Preferred Language</th><td>{{ $client->preferred_language }}</td></tr>
                                    <tr><th>Managing Agent</th><td>{{ $client->managing_agent }}</td></tr>
                                    <tr><th>Subscription Status</th><td>{{ $client->subscription_status }}</td></tr>
                                    <tr><th>Created</th><td>{{ $client->created_at ? $client->created_at->format('M d, Y') : 'N/A' }}</td></tr>
                                    <tr><th>Updated</th><td>{{ $client->updated_at ? $client->updated_at->format('M d, Y') : 'N/A' }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Buyer & Tenant Profile</div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr><th>Category</th><td>{{ $client->category }}</td></tr>
                                    <tr><th>Type</th><td>{{ $client->type }}</td></tr>
                                    <tr><th>Area</th><td>{{ $client->area }}</td></tr>
                                    <tr><th>Purchase Budget</th><td>{{ $client->purchase_budget }}</td></tr>
                                    <tr><th>Ability to Proceed</th><td>{{ $client->ability }}</td></tr>
                                    <tr><th>Reason for Buying</th><td>{{ $client->reasons_for_buying }}</td></tr>
                                    <tr><th>Potential</th><td>{{ $client->potential }}</td></tr>
                                    <tr><th>Time Frame</th><td>{{ $client->time_frame }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">Contacts</div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Mobile</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Notifications</div>
                        <div class="card-body">
                            <p class="text-danger">
                                @if($client->email_matching)
                                    Email matching properties automatically: Yes
                                @else
                                    Client has not consented to receiving marketing emails.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="matches" role="tabpanel" aria-labelledby="matches-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5></h5>
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Filters
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <!-- Add possible filter fields -->
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="category" id="filterCategory">
                            <label class="form-check-label" for="filterCategory">Category</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="branch" id="filterBranch">
                            <label class="form-check-label" for="filterBranch">Branch</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="proptype" id="filterProptype">
                            <label class="form-check-label" for="filterProptype">Property Type</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="floor" id="filterFloor">
                            <label class="form-check-label" for="filterFloor">Floor</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="bathrooms" id="filterBathrooms">
                            <label class="form-check-label" for="filterBathrooms">Bathrooms</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="bedrooms" id="filterBedrooms">
                            <label class="form-check-label" for="filterBedrooms">Bedrooms</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="parkingSpaces" id="filterParkingSpaces">
                            <label class="form-check-label" for="filterParkingSpaces">Parking Spaces</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="furnished" id="filterFurnished">
                            <label class="form-check-label" for="filterFurnished">Furnished</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="price" id="filterPrice">
                            <label class="form-check-label" for="filterPrice">Price</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="yearConstruction" id="filterYearConstruction">
                            <label class="form-check-label" for="filterYearConstruction">Year of Construction</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check ms-3">
                            <input class="form-check-input filter-checkbox" type="checkbox" value="energyEfficiency" id="filterEnergyEfficiency">
                            <label class="form-check-label" for="filterEnergyEfficiency">Energy Efficiency</label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Property</th>
                    <th>Area</th>
                    <th>Price/Rent</th>
                    <th>Date</th>
                    <th class="filter-column" data-column="category" style="display: none;">Category</th>
                    <th class="filter-column" data-column="branch" style="display: none;">Branch</th>
                    <th class="filter-column" data-column="proptype" style="display: none;">Property Type</th>
                    <th class="filter-column" data-column="floor" style="display: none;">Floor</th>
                    <th class="filter-column" data-column="bathrooms" style="display: none;">Bathrooms</th>
                    <th class="filter-column" data-column="bedrooms" style="display: none;">Bedrooms</th>
                    <th class="filter-column" data-column="parkingSpaces" style="display: none;">Parking Spaces</th>
                    <th class="filter-column" data-column="furnished" style="display: none;">Furnished</th>
                    <th class="filter-column" data-column="price" style="display: none;">Price</th>
                    <th class="filter-column" data-column="yearConstruction" style="display: none;">Year of Construction</th>
                    <th class="filter-column" data-column="energyEfficiency" style="display: none;">Energy Efficiency</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($matches as $match)
                    <tr>
                        <td>
                            @php
                                $photos = $match->photos ? json_decode($match->photos, true) : [];
                                $thumbnail = !empty($photos) ? asset('storage/' . $photos[0]) : asset('images/placeholder.png');
                            @endphp
                            <img
                                src="{{ $thumbnail }}"
                                alt="Thumbnail"
                                style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                data-bs-toggle="modal"
                                data-bs-target="#imageModal"
                                onclick="setModalImage('{{ $thumbnail }}')"
                            >
                        </td>
                        <td>#{{ $match->id }}</td>
                        <td>{{ $match->area }}</td>
                        <td>€{{ number_format($match->price, 2) }}</td>
                        <td>{{ $match->created_at->format('M d, Y') }}</td>
                        <td class="filter-column" data-column="category" style="display: none;">{{ $match->category ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="branch" style="display: none;">{{ $match->branch ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="proptype" style="display: none;">{{ $match->proptype ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="floor" style="display: none;">{{ $match->floor ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="bathrooms" style="display: none;">{{ $match->bathrooms ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="bedrooms" style="display: none;">{{ $match->bedrooms ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="parkingSpaces" style="display: none;">{{ $match->parkingSpaces ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="furnished" style="display: none;">{{ $match->furnished ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="price" style="display: none;">€{{ number_format($match->price ?? 0, 2) }}</td>
                        <td class="filter-column" data-column="yearConstruction" style="display: none;">{{ $match->yearConstruction ?? 'N/A' }}</td>
                        <td class="filter-column" data-column="energyEfficiency" style="display: none;">{{ $match->energyEfficiency ?? 'N/A' }}</td>
                        <td>
                            <form method="POST" action="{{ route('matches.update-status', $match->id) }}">
                                @csrf
                                <select name="status" required>
                                    <option value="Pending Approval" {{ $match->status == 'Pending Approval' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="Approved" {{ $match->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Dismissed" {{ $match->status == 'Dismissed' ? 'selected' : '' }}>Dismissed</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="17" class="text-center">No matches found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Thumbnail" class="img-fluid">
      </div>
    </div>
  </div>
</div>


<script>
function setModalImage(imageSrc) {
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
}

document.addEventListener('DOMContentLoaded', function () {
        const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const columnClass = `.filter-column[data-column="${this.value}"]`;
                const columns = document.querySelectorAll(columnClass);
                if (this.checked) {
                    columns.forEach(col => col.style.display = '');
                } else {
                    columns.forEach(col => col.style.display = 'none');
                }
            });
        });
    });
</script>
@endsection
