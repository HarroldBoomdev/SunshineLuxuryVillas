@extends('layouts.app')

@section('content')


<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Matches</h1>
        <!-- Filter Dropdown -->
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Filters
            </button>
            <button id="resetFilters" class="btn btn-danger">RESET</button>
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
                <th>Reference No.</th>
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
                            $photos = json_decode($match->photos, true);
                            $thumbnail = (!empty($photos) && isset($photos[0])) ? $photos[0] : asset('images/placeholder.png');
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

                    <td>{{ $match->reference }}</td>
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
                    <td colspan="10" class="text-center">No matches found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $matches->links() }}
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

document.addEventListener('DOMContentLoaded', function () {
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    const resetButton = document.getElementById('resetFilters');

    // Function to reset filters
    resetButton.addEventListener('click', function () {
        filterCheckboxes.forEach(checkbox => {
            checkbox.checked = false; // Uncheck all checkboxes
            const columnClass = `.filter-column[data-column="${checkbox.value}"]`;
            document.querySelectorAll(columnClass).forEach(col => col.style.display = 'none'); // Hide all filter columns
        });
    });

    // Function to show/hide columns based on checkboxes
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
