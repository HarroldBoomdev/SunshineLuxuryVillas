<!-- AREAS -->
<div class="card shadow-sm border-0 mb-4">
  <h3 class="mb-4 text-sm font-semibold text-gray-700">Distances</h3>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    {{-- Amenities (km) -> amenities --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Amenities (km)</span>
      <input
        type="number" step="0.1" min="0"
        name="amenities"
        value="{{ old('amenities', $property->amenities ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Airport (km) -> airport --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Airport (km)</span>
      <input
        type="number" step="0.1" min="0"
        name="airport"
        value="{{ old('airport', $property->airport ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Sea (km) -> sea --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Sea (km)</span>
      <input
        type="number" step="0.1" min="0"
        name="sea"
        value="{{ old('sea', $property->sea ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Public transport (km) -> publicTransport --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Public transport (km)</span>
      <input
        type="number" step="0.1" min="0"
        name="publicTransport"
        value="{{ old('publicTransport', $property->publicTransport ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Schools (km) -> schools --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Schools (km)</span>
      <input
        type="number" step="0.1" min="0"
        name="schools"
        value="{{ old('schools', $property->schools ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Resort (km) -> resort --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Resort (km)</span>
      <input
        type="number" step="0.1" min="0"
        name="resort"
        value="{{ old('resort', $property->resort ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>
  </div>

  <div class="card-header bg-white"><strong>Areas</strong></div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Covered m²</label>
        <input type="number" name="covered" class="form-control" step="0.01" min="0" value="{{ old('covered') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Plot m²</label>
        <input type="number" name="plot" class="form-control" step="0.01" min="0" value="{{ old('plot') }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Attic m²</label>
        <input type="number" name="attic" class="form-control" step="0.01" min="0" value="{{ old('attic') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Roof Garden m²</label>
        <input type="number" name="area_roof_garden" class="form-control" step="0.01" min="0" value="{{ old('area_roof_garden') }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Covered Veranda m²</label>
        <input type="number" name="coveredVeranda" class="form-control" step="0.01" min="0" value="{{ old('coveredVeranda') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Uncovered Veranda m²</label>
        <input type="number" name="uncoveredVeranda" class="form-control" step="0.01" min="0" value="{{ old('uncoveredVeranda') }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Covered Parking m²</label>
        <input type="number" name="coveredParking" class="form-control" step="0.01" min="0" value="{{ old('coveredParking') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Basement m²</label>
        <input type="number" name="basement" class="form-control" step="0.01" min="0" value="{{ old('basement') }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Courtyard m²</label>
        <input type="number" name="courtyard" class="form-control" step="0.01" min="0" value="{{ old('courtyard') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Garden m²</label>
        <input type="number" name="garden" class="form-control" step="0.01" min="0" value="{{ old('garden') }}">
      </div>
    </div>
  </div>
</div>






