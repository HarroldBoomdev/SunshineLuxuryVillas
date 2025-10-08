<!-- Distances (DB: amenities, airport, sea, publicTransport, schools, resort) -->
<div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
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
</div>
