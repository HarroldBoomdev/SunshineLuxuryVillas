@extends('layouts.app')

@section('content')

<!-- Tab UI (MUST be above search bar) -->
<div class="container mt-3" id="tabContainer" style="display: none;">
    <ul class="nav nav-tabs" id="pageTabs"></ul>
</div>
<div class="tab-content mt-2" id="tabContent"></div>



<div class="container mt-4">
    @include('layouts.newButton')

    <div class="container mt-4">
        <div class="card p-3 mb-4">
        <form id="propertySearchForm" action="{{ route('properties.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label for="reference" class="form-label">Reference</label>
                        <input type="text" name="reference" id="reference" class="form-control" placeholder="Enter Reference" value="{{ request('reference') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="country" class="form-label">Country</label>
                        <select name="country" id="country" class="form-control">
                            <option value="" selected>Not Specified</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="property_type" class="form-label">Property Type</label>
                        <select name="property_type" id="property_type" class="form-control">
                            <option value="" selected>Not Specified</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="min_price" class="form-label">Minimum Price</label>
                        <input type="number" name="min_price" id="min_price" class="form-control" placeholder="No Minimum" value="{{ request('min_price') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="max_price" class="form-label">Maximum Price</label>
                        <input type="number" name="max_price" id="max_price" class="form-control" placeholder="No Maximum" value="{{ request('max_price') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="bedrooms" class="form-label">Minimum Bedrooms</label>
                        <select name="bedrooms" id="bedrooms" class="form-control">
                            <option value="">No Minimum</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="bathrooms" class="form-label">Minimum Bathrooms</label>
                        <select name="bathrooms" id="bathrooms" class="form-control">
                            <option value="">No Minimum</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('bathrooms') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                        <div class="col-md-3">
                        <label for="bank" class="form-label">Bank</label>
                        <select name="bank" id="bank" class="form-control">
                            <option value="">All Banks</option>
                            <option value="Remu" {{ request('bank') == 'Remu' ? 'selected' : '' }}>Remu</option>
                            <option value="Altamira" {{ request('bank') == 'Altamira' ? 'selected' : '' }}>Altamira</option>
                            <option value="Altia" {{ request('bank') == 'Altia' ? 'selected' : '' }}>Altia</option>
                            <option value="Gogordian" {{ request('bank') == 'Gogordian' ? 'selected' : '' }}>Gogordian</option>
                            <option value="Alpha Bank" {{ request('bank') == 'Alpha Bank' ? 'selected' : '' }}>Alpha Bank</option>
                            <option value="Astro Bank" {{ request('bank') == 'Astro Bank' ? 'selected' : '' }}>Astro Bank</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-50 me-2">Search Properties</button>
                        <button type="reset" class="btn btn-secondary w-50" onclick="window.location.href='{{ route('properties.index') }}'">Reset Search</button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <a href="{{ route('properties.export', request()->query()) }}" class="btn btn-success w-100 ml-2">
                            Download Excel
                        </a>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#featuredModal">
                            Featured Properties
                        </button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#">
                            Featured Logs
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    <h1>
        Properties
        <small id="results-count">{{ $properties->total() }} results</small>
        @if(request()->filled('bank'))
            <span class="badge bg-info text-dark ms-2">Viewing: {{ request('bank') }}</span>
            <a href="{{ route('properties.index') }}" class="ms-2 text-danger small">Clear filter</a>
        @endif
    </h1>


    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="tabs">
        <!-- Tabs will be dynamically injected here -->
    </ul>

    <!-- Tab Content -->
    <div id="tab-content" class="mb-4">
        <!-- Content loaded via AJAX will appear here -->
    </div>



    <div id="properties-table">
        <div class="table-responsive">
            <div class="overflow-x-auto">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border text-center"><input type="checkbox" id="select-all"></th>
                            <th class="px-4 py-2 border">Thumbnail</th>
                            <th class="px-4 py-2 border">Reference #</th>
                            <th class="px-4 py-2 border">Title</th>
                            <th class="px-4 py-2 border">Location</th>
                            <th class="px-4 py-2 border">Bedrooms</th>
                            <th class="px-4 py-2 border">Price (€)</th>
                            <th class="px-4 py-2 border">Plot Size (m²)</th>
                            <th class="px-4 py-2 border">Internal Area (m²)</th>
                            <th class="px-4 py-2 border">Bank</th>
                            <th class="px-4 py-2 border">Website Live</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($properties as $property)
                            <tr>
                                <td class="px-4 py-2 border text-center">
                                    <input type="checkbox" class="property-checkbox" value="{{ $property->id }}">
                                </td>
                                <td class="px-4 py-2 border">
                                    @php
                                        $photos = is_string($property->photos) ? json_decode($property->photos, true) : (is_array($property->photos) ? $property->photos : []);
                                        $photoUrl = '';
                                        if (!empty($photos) && is_array($photos)) {
                                            $photoUrl = is_string($photos[0]) ? $photos[0] : '';
                                        }
                                    @endphp
                                    @if (!empty($photoUrl))
                                        <img src="{{ $photoUrl }}" alt="Thumbnail" style="width:80px; height:auto;">
                                    @endif


                                </td>
                                <td class="px-4 py-2 border">{{ $property->reference }}</td>
                                <td class="px-4 py-2 border">{{ $property->title }}</td>
                                <td class="px-4 py-2 border">{{ $property->location ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">{{ $property->bedrooms ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">
                                    @if($property->price)
                                        €{{ number_format($property->price, 2) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 border">{{ $property->plot_size ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">{{ $property->internal_area ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">
                                    @php
                                        $ref = strtoupper($property->reference);
                                        $bank = '';

                                        if (str_ends_with($ref, 'ABPIR')) {
                                            $bank = 'Astro Bank';
                                        } elseif (str_ends_with($ref, 'AB')) {
                                            $bank = 'Alpha Bank';
                                        } elseif (str_ends_with($ref, 'GG')) {
                                            $bank = 'Gogordian';
                                        } elseif (str_ends_with($ref, 'ALT') || str_ends_with($ref, 'AT')) {
                                            $bank = 'Altia';
                                        } elseif (str_ends_with($ref, 'A')) {
                                            $bank = 'Altamira';
                                        } elseif (str_ends_with($ref, 'R')) {
                                            $bank = 'Remu';
                                        }
                                    @endphp

                                    @if ($bank)
                                        <a href="{{ route('properties.index', ['bank' => $bank]) }}" class="text-primary text-decoration-underline">
                                            {{ $bank }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif

                                </td>

                                <td class="px-4 py-2 border text-center">{{ $property->is_live ? '✅' : '❌' }}</td>
                                <td class="px-4 py-2 border">
                                    @can('property.view')
                                        <a  href="{{ route('properties.show', $property->id) }}"
                                            target="_blank" rel="noopener"
                                            class="btn btn-sm btn-info"
                                            title="View"
                                            onclick="openTab('#{{ $property->id }} - {{ Str::limit($property->title, 20) }}', this.href);">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endcan


                                    @can('property.edit')
                                        <a href="{{ route('properties.edit', $property->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan

                                    @can('property.delete')
                                        <form action="{{ route('properties.destroy', $property->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center">No properties found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    </div>
    @include('properties.partials.featured-modal')
</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('propertySearchForm');
        const inputs = ['reference', 'country', 'property_type', 'min_price', 'max_price', 'bedrooms', 'bathrooms', 'bank'];

        // Attach change and input listeners for auto AJAX
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', submitPropertyForm);
                el.addEventListener('change', submitPropertyForm);
            }
        });

        // AJAX fetch logic
        function submitPropertyForm() {
            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();

            fetch(form.action + '?' + queryString, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.properties) {
                    document.getElementById('properties-table').innerHTML = data.properties;
                    document.getElementById('results-count').innerText = `${data.total} results`;
                }
            });
        }

        // Select all checkbox toggle
        document.getElementById("select-all")?.addEventListener("change", function () {
            document.querySelectorAll(".property-checkbox").forEach(cb => cb.checked = this.checked);
        });
    });


    function openTab(title, url) {
    const tabContainer = document.getElementById("tabContainer");
    const pageTabs = document.getElementById("pageTabs");
    const tabContent = document.getElementById("tabContent");

    const tabId = title.replace(/\s+/g, '-').toLowerCase();

    // If already exists, activate it
    if (document.getElementById(`tab-${tabId}`)) {
        new bootstrap.Tab(document.querySelector(`#tab-${tabId}-link`)).show();
        return;
    }

    tabContainer.style.display = "block";

    // Deactivate all existing tabs
    pageTabs.querySelectorAll(".nav-link").forEach(tab => tab.classList.remove("active"));
    tabContent.querySelectorAll(".tab-pane").forEach(pane => pane.classList.remove("show", "active"));

    // Create the tab
    const newTab = document.createElement("li");
    newTab.className = "nav-item";
    newTab.innerHTML = `
        <a class="nav-link active" id="tab-${tabId}-link" data-bs-toggle="tab" href="#tab-${tabId}" role="tab">
            ${title}
            <button type="button" class="btn-close ms-2" aria-label="Close" onclick="closeTab('${tabId}')"></button>
        </a>
    `;
    pageTabs.appendChild(newTab);

    // Create the tab content pane
    const newTabPane = document.createElement("div");
    newTabPane.className = "tab-pane fade show active"; // ← THIS IS THE FIX
    newTabPane.id = `tab-${tabId}`;
    newTabPane.innerHTML = `<div class="text-center my-3">Loading...</div>`;
    tabContent.appendChild(newTabPane);

    // Load content from server
    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const content = doc.querySelector('.content-wrapper') || doc.body;
            newTabPane.innerHTML = content.innerHTML;
        })
        .catch(() => {
            newTabPane.innerHTML = "<p class='text-danger'>Error loading content.</p>";
        });
}

</script>

@push('scripts')
<script>
    function openTab(title, url) {
        const tabContainer = document.getElementById('tabs');
        const contentContainer = document.getElementById('tab-content');

        // If tab already exists, switch to it
        let existingTab = document.querySelector(`.nav-link[data-url="${url}"]`);
        if (existingTab) {
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            existingTab.classList.add('active');

            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            document.getElementById(existingTab.dataset.id).classList.add('active');
            return;
        }

        // Generate unique ID
        const tabId = 'tab_' + Math.random().toString(36).substr(2, 9);

        // Create tab
        const newTab = document.createElement('li');
        newTab.className = 'nav-item';
        newTab.innerHTML = `
            <a class="nav-link active" data-id="${tabId}" data-url="${url}" href="#">${title}
                <button onclick="closeTab('${tabId}', event)" class="ml-2 text-red-600">&times;</button>
            </a>
        `;
        tabContainer.appendChild(newTab);

        // Deactivate other tabs
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));

        // Create content pane
        const newPane = document.createElement('div');
        newPane.className = 'tab-pane active';
        newPane.id = tabId;
        newPane.innerHTML = '<p>Loading...</p>';
        contentContainer.appendChild(newPane);

        // Deactivate other content panes
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

        // Load content via AJAX
        fetch(url)
            .then(response => response.text())
            .then(html => {
                newPane.innerHTML = html;
            })
            .catch(err => {
                newPane.innerHTML = '<p>Error loading content.</p>';
            });
    }

    function closeTab(tabId, event) {
        event.preventDefault();
        event.stopPropagation();

        const tab = document.querySelector(`.nav-link[data-id="${tabId}"]`);
        const pane = document.getElementById(tabId);

        tab.parentElement.remove();
        pane.remove();

        // Activate the last tab if any
        const lastTab = document.querySelector('.nav-link:last-child');
        if (lastTab) {
            lastTab.classList.add('active');
            const lastPane = document.getElementById(lastTab.dataset.id);
            if (lastPane) lastPane.classList.add('active');
        }
    }
</script>
@endpush



@endsection
