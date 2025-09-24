@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <form id="editPropertyForm" action="{{ route('properties.update', $property->id) }}" method="POST">
    @csrf
    @method('PUT')
    @include('layouts.newButton')


    <div class="row">
    <h1>Edit Property Details</h1>
        <!-- Left Column: Details -->
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="row g-3">
                    <div class="card p-3">
                        <h4>Details</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_type">Property Type *</label>
                                    <select id="property_type" name="property_type" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['Apartment', 'Bungalow', 'ComProp' => 'Commercial Property', 'Investment' => 'Investment Property', 'Penthouse', 'Plot', 'Studio', 'Townhouse', 'Villa'] as $key => $label)
                                            <option value="{{ is_numeric($key) ? $label : $key }}"
                                                {{ $property->property_type == (is_numeric($key) ? $label : $key) ? 'selected' : '' }}>
                                                {{ is_numeric($key) ? $label : $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="protype">Label</label>
                                    <select id="protype" name="protype" class="form-control" required>
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['detached' => 'Detached House', 'semi-detached' => 'Semi-Detached House', 'link-detached' => 'Link-Detached House', 'house' => 'House', 'houseofchar' => 'House of Character', 'apartment' => 'Apartment', 'duplexmaisonette' => 'Duplex Maisonette'] as $key => $label)
                                            <option value="{{ $key }}" {{ $property->protype == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_floor">Floor</label>
                                    <select id="edit_floor" name="floor" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['apartment', 'basement', 'semi-basement', 'groundFloor', 'topFloor', 'penthouse', '1', '2'] as $floor)
                                            <option value="{{ $floor }}" {{ $property->floor == $floor ? 'selected' : '' }}>{{ ucfirst($floor) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_parkingSpaces">Parking Spaces</label>
                                    <select id="edit_parkingSpaces" name="parkingSpaces" class="form-control">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ $property->parkingSpaces == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_kitchens">Kitchens</label>
                                    <select id="edit_kitchens" name="kitchens" class="form-control">
                                        @for ($i = 1; $i <= 3; $i++)
                                            <option value="{{ $i }}" {{ $property->kitchens == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_bathrooms">Bathrooms</label>
                                    <select id="edit_bathrooms" name="bathrooms" class="form-control">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ $property->bathrooms == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_toilets">Toilets</label>
                                    <select id="edit_toilets" name="toilets" class="form-control">
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ $property->toilets == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_furnished">Furnished</label>
                                    <select id="edit_furnished" name="furnished" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['fullyFurnished' => 'Fully Furnished', 'partiallyFurnished' => 'Partially Furnished', 'unfurnished' => 'Unfurnished', 'optionalFurnished' => 'Optional Furnished'] as $key => $label)
                                            <option value="{{ $key }}" {{ $property->furnished == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_orientation">Orientation</label>
                                    <select id="edit_orientation" name="orientation" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['east', 'eastwest', 'eastmeridian', 'north', 'northeast', 'northwest', 'west'] as $orientation)
                                            <option value="{{ $orientation }}" {{ $property->orientation == $orientation ? 'selected' : '' }}>{{ ucfirst($orientation) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_yearRenovation">Year of Renovation</label>
                                    <select id="edit_yearRenovation" name="yearRenovation" class="form-control">
                                        <option value="" selected disabled>-- Select Year --</option>
                                        @for ($i = 1995; $i <= 2025; $i++)
                                            <option value="{{ $i }}" {{ $property->yearRenovation == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_price">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input
                                            type="text"
                                            id="edit_price"
                                            name="price"
                                            class="form-control"
                                            value="{{ $property->price }}"
                                            placeholder="Enter amount in EUR"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '');"
                                        >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="edit_vat">VAT</label>
                                    <select id="edit_vat" name="vat" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        <option value="yes" {{ $property->vat == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_referrence">Reference</label>
                                    <input type="text" id="edit_referrence" name="referrence" class="form-control" value="{{ $property->referrence }}">
                                </div>
                                <div class="form-group">
                                    <label for="edit_managing_agent">Managing Agent *</label>
                                    <select id="edit_managing_agent" name="managing_agent" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['Thomas Harrison'] as $agent)
                                            <option value="{{ $agent }}" {{ $property->managing_agent == $agent ? 'selected' : '' }}>{{ $agent }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" id="edit_floors-group" style="display: none;">
                                    <label for="edit_floors">Floors</label>
                                    <select id="edit_floors" name="floors" class="form-control">
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ $property->floors == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_labels">Labels</label>
                                    <div class="dropdown">
                                        <div class="form-control dropdown-toggle" id="edit_proplabelsField" data-bs-toggle="dropdown" aria-expanded="false" readonly>
                                        </div>
                                        <div class="dropdown-menu p-3" style="width: 100%;">
                                            @foreach(['Luxury', 'Pet Friendly', 'Bank', 'KML', 'Residential Sales', 'Repossession', 'High Rental Yield', 'Prestige', 'Exclusive', 'Reserved', 'Sold', 'Reduced'] as $label)
                                                <div class="form-check">
                                                    <input type="checkbox" class="edit_proplabelsField-checkbox" id="edit_{{ strtolower(str_replace(' ', '_', $label)) }}" name="labels[]" value="{{ $label }}" {{ in_array($label, $property->labels ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_{{ strtolower(str_replace(' ', '_', $label)) }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="edit_status">Status</label>
                                    <select id="edit_status" name="status" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['resale', 'brandnew', 'completed', 'keyready', 'undrconstruction', 'incomplete', 'offplan'] as $status)
                                            <option value="{{ $status }}" {{ $property->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_livingRooms">Living Rooms</label>
                                    <select id="edit_livingRooms" name="livingRooms" class="form-control">
                                        @for ($i = 1; $i <= 3; $i++)
                                            <option value="{{ $i }}" {{ $property->livingRooms == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_bedrooms">Bedrooms</label>
                                    <select id="edit_bedrooms" name="bedrooms" class="form-control">
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ $property->bedrooms == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_showers">Showers</label>
                                    <select id="edit_showers" name="showers" class="form-control">
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ $property->showers == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_basement">Basement</label>
                                    <select id="edit_basement" name="basement" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        <option value="yes" {{ $property->basement == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $property->basement == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_yearConstruction">Year of Construction</label>
                                    <select id="edit_yearConstruction" name="yearConstruction" class="form-control">
                                        @for ($i = 1995; $i <= 2025; $i++)
                                            <option value="{{ $i }}" {{ $property->yearConstruction == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_energyEfficiency">Energy Efficiency Rating</label>
                                    <select id="edit_energyEfficiency" name="energyEfficiency" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['exempt', 'certExcepted', 'A', 'B+', 'C', 'D', 'E', 'F', 'G', 'H'] as $rating)
                                            <option value="{{ $rating }}" {{ $property->energyEfficiency == $rating ? 'selected' : '' }}>{{ $rating }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_communalCharge">Communal Charge</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="text" id="edit_communalCharge" name="communalCharge" class="form-control" value="{{ $property->communalCharge }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="edit_comChargeFreq">Communal Charge Frequency</label>
                                    <select id="edit_comChargeFreq" name="comChargeFreq" class="form-control">
                                        <option value="" disabled="disabled">-</option>
                                        @foreach(['monthly', 'quarterly', 'semiAnnually', 'annually'] as $freq)
                                            <option value="{{ $freq }}" {{ $property->comChargeFreq == $freq ? 'selected' : '' }}>{{ ucfirst($freq) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="edit_commission">Commission</label>
                                    <input type="number" id="edit_commission" name="commission" class="form-control" value="{{ $property->commission }}">
                                </div>
                            </div>
                        </div>

                    <h4>Areas</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                @foreach(['covered' => 'Covered', 'attic' => 'Attic', 'coveredVeranda' => 'Covered Veranda', 'coveredParking' => 'Covered Parking', 'courtyard' => 'Courtyard'] as $key => $label)
                                    <div class="form-group">
                                        <label for="edit_{{ $key }}">{{ $label }} m2</label>
                                        <input type="text" id="edit_{{ $key }}" name="{{ $key }}" class="form-control" value="{{ $property->$key }}">
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                @foreach(['basement' => 'Basement', 'roofGarden' => 'Roof Garden', 'uncoveredVeranda' => 'Uncovered Veranda', 'plot' => 'Plot', 'garden' => 'Garden'] as $key => $label)
                                    <div class="form-group">
                                        <label for="edit_{{ $key }}">{{ $label }} m2</label>
                                        <input type="text" id="edit_{{ $key }}" name="{{ $key }}" class="form-control" value="{{ $property->$key }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    @php
                        $selectedFacilities = is_array($property->facilities) ? $property->facilities : explode(',', $property->facilities ?? '');
                        $selectedHeating = is_array($property->heating) ? $property->heating : explode(',', $property->heating ?? '');
                        $selectedPool = is_array($property->pool) ? $property->pool : explode(',', $property->pool ?? '');
                    @endphp

                    <div class="form-group">
                        <label for="edit_facilities">Facilities</label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="edit_facilitiesDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Select Facilities
                            </button>
                            <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="edit_facilitiesDropdownButton">
                                @foreach(['Central System', 'Split System', 'Provision', 'Elevator', 'Gated Complex', 'Children Playground', 'Gym'] as $facility)
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_{{ str_replace(' ', '_', strtolower($facility)) }}" name="facilities[]" value="{{ $facility }}" {{ in_array($facility, $selectedFacilities) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_{{ str_replace(' ', '_', strtolower($facility)) }}">{{ $facility }}</label>
                                        </div>
                                    </li>
                                @endforeach

                                <li><h4>Heating</h4></li>
                                @foreach(['Central', 'Central, Independent', 'Central, Electric', 'Split System', 'Underfloor', 'Provision'] as $heating)
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_{{ str_replace([', ', ' '], '_', strtolower($heating)) }}" name="heating[]" value="{{ $heating }}" {{ in_array($heating, $selectedHeating) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_{{ str_replace([', ', ' '], '_', strtolower($heating)) }}">{{ $heating }}</label>
                                        </div>
                                    </li>
                                @endforeach

                                <li><h4>Indoor Pool</h4></li>
                                @foreach(['Private', 'Private, Overflow', 'Private, Heated', 'Private, Salt Water', 'Communal', 'Communal, Overflow', 'Communal, Heated', 'Communal, Salt Water'] as $pool)
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_{{ str_replace([', ', ' '], '_', strtolower($pool)) }}" name="pool[]" value="{{ $pool }}" {{ in_array($pool, $selectedPool) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_{{ str_replace([', ', ' '], '_', strtolower($pool)) }}">{{ $pool }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                <div class="card p-3">
                    <div class="form-group">
                        <label for="edit_labels">Facilities</label>
                        <div class="mb-3 d-flex justify-content-end">
                            <select id="edit_languageSelect" class="form-select w-auto">
                                @foreach(['english', 'spanish', 'french', 'german'] as $lang)
                                    <option value="{{ $lang }}" {{ $property->language == $lang ? 'selected' : '' }}>{{ ucfirst($lang) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_titleInput" class="form-label">Title</label>
                            <div class="d-flex align-items-center">
                                <input type="text" id="edit_titleInput" name="title" class="form-control me-2" value="{{ $property->title }}" required>
                                <button type="button" class="btn btn-generate">
                                    <i class="fas fa-robot me-1"></i> Generate with AI
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_propertyDescriptionInput" class="form-label">Property Description</label>
                            <textarea id="edit_propertyDescriptionInput" name="property_description" class="form-control" rows="3">{{ $property->property_description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card p-3">
                    <div class="form-group">
                        <label for="edit_notes">Notes</label>
                        <textarea class="form-control notes-area" name="notes" rows="4">{{ $property->notes }}</textarea>
                    </div>
                </div>

                <div class="card p-3">
                    <div class="form-group">
                        <h6>Kuula</h6>
                        <label for="edit_kuula_link" class="form-label">Virtual Tour Link</label>
                        <input type="url" id="edit_kuula_link" name="kuula_link" class="form-control" value="{{ $property->kuula_link }}">
                    </div>
                </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                </div>

                @php
                    $photos = is_array($property->photos)
                        ? $property->photos
                        : (is_string($property->photos) ? explode(',', $property->photos) : []);
                @endphp

                <div class="row">
                    <div class="card p-3">
                        <h4>Photos</h4>
                        <div class="gallery-upload-section">
                            <input type="file" id="edit_photos" name="photos[]" class="form-control" multiple>
                        </div>

                        <div class="gallery-grid mt-3" id="galleryGrid">
                            @foreach($photos as $photo)
                                @if(!empty($photo)) {{-- Ensure the photo is not an empty string --}}
                                    <img src="{{ Storage::disk('public')->exists($photo) ? asset('storage/' . $photo) : asset($photo) }}" class="img-thumbnail" alt="Property Image">
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="card p-3">
                        <h4>Owner</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_owner">Owner</label>
                                <input type="text" id="edit_owner" name="owner" class="form-control" value="{{ $property->owner }}">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_refId">External Reference ID</label>
                                <input type="text" id="edit_refId" name="refId" class="form-control" value="{{ $property->refId }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="card p-3">
                        <h4>Location</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_region">Region</label>
                                <input type="text" id="edit_region" name="region" class="form-control" value="{{ $property->region }}">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_town">Town/City</label>
                                <input type="text" id="edit_town" name="town" class="form-control" value="{{ $property->town }}">
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#mapModal">Select Location</button>
                            </div>
                            <!-- Edit Map Modal -->
                            <div class="modal fade" id="edit_mapModal" tabindex="-1" aria-labelledby="edit_mapModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="max-width: 1200px !important; margin-left: -350px;">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="edit_mapModalLabel">Select Location</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="edit_map" style="height: 500px; width: 1170px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-secondary">Upload KML</button>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="edit_latitude">Latitude</label>
                                    <input type="text" id="edit_latitude" name="latitude" class="form-control" value="{{ $property->latitude }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_longitude">Longitude</label>
                                    <input type="text" id="edit_longitude" name="longitude" class="form-control" value="{{ $property->longitude }}" readonly>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label for="edit_accuracy">Accuracy</label>
                                <select id="edit_accuracy" name="accuracy" class="form-control">
                                    @foreach(['pinpoint', '100m', '200m', '300m', '400m', '500m', '600m', '700m', '800m', '900m', '1000m'] as $accuracy)
                                        <option value="{{ $accuracy }}" {{ $property->accuracy == $accuracy ? 'selected' : '' }}>{{ ucfirst($accuracy) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    <!-- Edit Land Information -->
                        <h4>Land</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_regnum">Registration Number</label>
                                    <input type="text" id="edit_regnum" name="edit_regnum" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="edit_plotnum">Plot Number</label>
                                    <input type="text" id="edit_plotnum" name="edit_plotnum" class="form-control">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="edit_titleDead">Title Deed</label>
                                    <select id="edit_titleDead" name="edit_titleDead" class="form-control">
                                        <option value="" disabled selected>-</option>
                                        <option value="yes">Yes</option>
                                        <option value="shareOfLand">Share of Land</option>
                                        <option value="finalApproval">Final Approval</option>
                                        <option value="leaseHold">Lease Hold</option>
                                        <option value="land">Land</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_section">Section</label>
                                    <input type="text" id="edit_section" name="edit_section" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="edit_sheetPlan">Sheet/Plan</label>
                                    <input type="text" id="edit_sheetPlan" name="edit_sheetPlan" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="edit_share">Share</label>
                                    <input type="number" id="edit_share" name="edit_share" class="form-control">
                                </div>
                            </div>

                            <!-- Edit Title Deed Image Upload -->
                        <div class="gallery-container">
                            <h5>Title Deed Image</h5>
                            <div class="gallery-upload-section">
                                <div class="title-upload-box" onclick="document.getElementById('edit_titleDeedImage').click()">
                                    <i class="icon-image-placeholder"></i>
                                    <p>Drag or click to choose a file</p>
                                    <p class="text-muted">Maximum file size: 30MB</p>
                                </div>
                                <input type="file" id="edit_titleDeedImage" name="edit_titledeed[]" class="form-control" multiple accept="image/*" style="display: none;">
                                <div class="gallery-grid mt-3" id="edit_titleGrid">
                                    <!-- Image previews will appear here -->
                                </div>
                            </div>
                            <button id="edit_doneTitle" class="btn btn-success mt-3">Done</button>
                        </div>

                        <!-- Edit Distances -->
                        <div class="card p-3">
                            <h4>Distances</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_amenities">Amenities (km)</label>
                                        <input type="text" id="edit_amenities" name="edit_amenities" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_sea">Sea (km)</label>
                                        <input type="text" id="edit_sea" name="edit_sea" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_schools">Schools (km)</label>
                                        <input type="text" id="edit_schools" name="edit_schools" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_airport">Airport (km)</label>
                                        <input type="text" id="edit_airport" name="edit_airport" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_publicTransport">Public Transport (km)</label>
                                        <input type="text" id="edit_publicTransport" name="edit_publicTransport" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_resort">Resort (km)</label>
                                        <input type="text" id="edit_resort" name="edit_resort" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Features Dropdown -->
                        <div class="card p-3">
                            <div class="form-group">
                                <label for="edit_labelsDropdownButton">Features</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="edit_labelsDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Features
                                    </button>
                                    <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="edit_labelsDropdownButton">
                                        <li><h4>Options</h4></li>
                                        <script>
                                            const features = [
                                                { id: "edit_centralSystem", name: "Central System" },
                                                { id: "edit_splitSystem", name: "Split System" },
                                                { id: "edit_provision", name: "Provision" },
                                                { id: "edit_elevator", name: "Elevator" },
                                                { id: "edit_gatedComplex", name: "Gated Complex" },
                                                { id: "edit_childrenPlayground", name: "Children Playground" },
                                                { id: "edit_gym", name: "Gym" }
                                            ];

                                            features.forEach(feature => {
                                                document.write(`
                                                    <li>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="${feature.id}" name="edit_labels[]" value="${feature.name}">
                                                            <label class="form-check-label" for="${feature.id}">${feature.name}</label>
                                                        </div>
                                                    </li>
                                                `);
                                            });
                                        </script>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Floor Plans -->
                        <div class="card p-3">
                            <h4>Floor Plans</h4>
                            <div class="gallery-container">
                                <div class="gallery-upload-section">
                                    <div class="gallery-upload-box" onclick="document.getElementById('edit_floorPlans').click()">
                                        <i class="icon-image-placeholder"></i>
                                        <p>Drag or click to choose files</p>
                                        <p class="text-muted">Maximum file size: 30MB</p>
                                    </div>
                                    <input
                                        type="file"
                                        id="edit_floorPlans"
                                        name="edit_floor_plans[]"
                                        class="form-control d-none"
                                        multiple
                                        accept="image/*"
                                    />
                                    <div class="gallery-grid mt-3" id="edit_floorplanGrid">
                                        <!-- Preview of selected images will appear here -->
                                    </div>
                                </div>
                                <button id="edit_doneFloor" class="btn btn-success mt-3">Done</button>
                            </div>
                        </div>

                        <!-- Edit Videos -->
                        <div class="card p-3">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6>Videos</h6>
                                    <button id="edit_addVideo" class="btn btn-success mt-3 mb-3">+ Add</button>
                                </div>
                                <input type="url" id="edit_videoURL" class="form-control" placeholder="https://youtu.be/xxxxxxxxxx">
                            </div>
                        </div>

                        <!-- Edit Virtual Tour -->
                        <div class="card p-3">
                            <div class="form-group">
                                <label for="edit_matterportLink" class="form-label">Virtual Tour Link</label>
                                <input type="url" id="edit_matterportLink" class="form-control" placeholder="https://my.matterport.com/xxxxxxxx">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit_propertyForm');
        if (!form) {
            console.error('Form with id "edit_propertyForm" not found.');
            return;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response from server:', data);
                if (data.success) {
                    window.location.href = '/properties'; // Redirect after successful update
                } else {
                    console.error('Save failed:', data);
                    alert('Failed to save data. Check console for details.');
                }
            })
            .catch(error => {
                console.error('Error occurred:', error);
                alert('An error occurred. Check console for details.');
            });
        });

        /** Handle Image Previews */
        function handleImagePreview(inputId, gridId, isMultiple = true) {
            const input = document.getElementById(inputId);
            const grid = document.getElementById(gridId);

            if (!input || !grid) {
                console.error(`Elements with id "${inputId}" or "${gridId}" not found.`);
                return;
            }

            input.addEventListener('change', function () {
                grid.innerHTML = ''; // Clear previous previews
                const files = isMultiple ? Array.from(input.files) : [input.files[0]];

                files.forEach(file => {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '100px';
                            img.style.margin = '5px';
                            img.classList.add('img-thumbnail');
                            grid.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        console.error('Invalid file selected.');
                    }
                });
            });
        }

        // Initialize previews for edit mode
        handleImagePreview('edit_photos', 'edit_galleryGrid');
        handleImagePreview('edit_floorPlans', 'edit_floorplanGrid');
        handleImagePreview('edit_titleDeedImage', 'edit_titleGrid', false);

        /** Handle Dynamic Video Inputs */
        document.getElementById('edit_addVideo').addEventListener('click', function () {
            const videoInput = document.createElement('input');
            videoInput.type = 'url';
            videoInput.className = 'form-control mt-2';
            videoInput.placeholder = "https://youtu.be/xxxxxxxxxx";
            document.querySelector('.form-group').appendChild(videoInput);
        });

        /** Initialize Map */
        function initializeMap() {
            if (window.editMap) {
                window.editMap.invalidateSize();
                return;
            }

            window.editMap = L.map('edit_map').setView([51.505, -0.09], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(window.editMap);

            const marker = L.marker([51.505, -0.09], { draggable: true }).addTo(window.editMap);

            marker.on('dragend', function (e) {
                const latlng = e.target.getLatLng();
                updateCoordinates(latlng.lat, latlng.lng);
            });

            function updateCoordinates(lat, lng) {
                document.getElementById('edit_latitude').value = lat.toFixed(6);
                document.getElementById('edit_longitude').value = lng.toFixed(6);
            }
        }

        document.querySelector('.btn-select-location').addEventListener('click', initializeMap);

        /** Handle Form Auto-fill (Title & Description Generation) */
        document.querySelectorAll('.btn-generate').forEach((button, index) => {
            button.addEventListener('click', function () {
                if (index === 0) generateTitle();
                else if (index === 1) generateDescription();
            });
        });

        function generateTitle() {
            const title = `${getValue('edit_category', 'Property')} - ${getValue('edit_protype', 'House')} in ${getValue('edit_branch', 'Unknown Location')} | ${getValue('edit_bedrooms', 'N/A')} Bed, ${getValue('edit_bathrooms', 'N/A')} Bath`;
            document.getElementById('edit_titleInput').value = title;
        }

        function generateDescription() {
            const description = `
                This stunning ${getValue('edit_furnished', 'Furnishing details not specified')} ${getValue('edit_protype', 'property')} boasts
                ${getValue('edit_covered', 'N/A')} m² covered area, a ${getValue('edit_roofGarden', 'N/A')} m² roof garden,
                and a ${getValue('edit_garden', 'N/A')} m² garden. It offers ${getValue('edit_parkingSpaces', 'No')} parking spaces
                and an energy efficiency rating of ${getValue('edit_energyEfficiency', 'Unspecified')}. ${getCheckedLabels().join(', ')}.
                Priced at €${getValue('edit_price', 'Contact for price')}, this property is perfect for modern living.
            `;
            document.getElementById('edit_textInput').value = description.trim();
        }

        function getValue(id, defaultValue = '') {
            const element = document.getElementById(id);
            return element ? (element.value || defaultValue) : defaultValue;
        }

        function getCheckedLabels() {
            return Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(box => box.value);
        }

        /** Handle Floor Selection */
        document.getElementById('edit_floor').addEventListener('change', function () {
            document.getElementById('edit_floors-group').style.display = this.value === 'apartment' ? 'block' : 'none';
        });

    });
</script>


@endsection