@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow-sm sm:rounded-lg p-6 bg-white">

            {{-- Only for Full Access Users --}}
            @if($fullAccess)
                <div class="mb-4">
                    <label class="font-semibold text-lg block mb-2">Select Widgets to Display</label>
                    <div class="flex flex-wrap gap-4">
                        <label><input type="checkbox" class="dashboard-toggle" value="sales-listings"> Sales & Listings</label>
                        <label><input type="checkbox" class="dashboard-toggle" value="sales"> Sales</label>
                        <label><input type="checkbox" class="dashboard-toggle" value="listings"> Listings</label>
                        <label><input type="checkbox" class="dashboard-toggle" value="executive"> Executive Summary</label>
                        <label><input type="checkbox" class="dashboard-toggle" value="map-listing"> Map Listing</label>
                        <button id="reset-widgets" class="ml-auto px-4 py-2 bg-gray-300 rounded">Reset</button>
                    </div>
                </div>
            @endif

            {{-- Widgets for Full Access (Hidden by Default) --}}
            @if($fullAccess)
                <div id="sales-listings-widget" style="display: none;" class="scroll-target">
                    @include('dashboard.user.sales_listings')
                </div>
                <div id="sales-widget" style="display: none;" class="scroll-target">
                    @include('dashboard.user.sales_only')
                </div>
                <div id="listings-widget" style="display: none;" class="scroll-target">
                    @include('dashboard.user.listings_only')
                </div>
                <div id="executive-widget" style="display: none;" class="scroll-target">
                    @include('dashboard.user.nicole_custom')
                </div>
                <div id="map-listing-widget" style="display: none;" class="scroll-target">
                    @include('dashboard.user.map_listing')
                </div>
            @endif

            {{-- Role-based Widgets for Restricted Users --}}
            @if(!$fullAccess && $salesOnly)
                <div id="sales-widget" class="mt-6 scroll-target">
                    @include('dashboard.user.sales_only')
                </div>
            @endif

            @if(!$fullAccess && $listingsOnly)
                <div id="listings-widget" class="mt-6 scroll-target">
                    @include('dashboard.user.listings_only')
                </div>
            @endif

            @if(!$fullAccess && $nicoleCustom)
                <div id="executive-widget" class="mt-6 scroll-target">
                    @include('dashboard.user.nicole_custom')
                </div>
            @endif

             {{-- Always show calendar --}}
            <div class="mt-6" id="calendar-widget">
                @include('dashboard.user.diary', ['agents' => $agents])
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.dashboard-toggle');
    const resetBtn = document.getElementById('reset-widgets');
    const preferences = @json(auth()->user()->dashboard_preferences ?? []);

    toggles.forEach(toggle => {
        const target = document.getElementById(`${toggle.value}-widget`);
        const show = preferences.includes(toggle.value);
        toggle.checked = show;
        if (target) target.style.display = show ? 'block' : 'none';
    });

    toggles.forEach(toggle => {
        toggle.addEventListener('change', () => {
            const prefs = Array.from(toggles)
                .filter(t => t.checked)
                .map(t => t.value);

            fetch('{{ route("dashboard.preferences") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ preferences: prefs })
            });

            const target = document.getElementById(`${toggle.value}-widget`);
            if (target) {
                target.style.display = toggle.checked ? 'block' : 'none';
                if (toggle.checked) {
                    setTimeout(() => target.scrollIntoView({ behavior: 'smooth' }), 100);
                }
            }
        });
    });

    resetBtn?.addEventListener('click', () => {
        toggles.forEach(toggle => {
            toggle.checked = false;
            const target = document.getElementById(`${toggle.value}-widget`);
            if (target) target.style.display = 'none';
        });

        fetch('{{ route("dashboard.preferences") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ preferences: [] })
        });
    });
});
</script>
@endpush
