<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">Developers Report</h2>
            <p class="text-sm text-gray-500">Developer performance overview (inventory + engagement).</p>
        </div>

        <div class="flex gap-2 items-center">
            <button class="btn btn-outline btn-sm">Data Comparison</button>

            <select class="form-select form-select-sm" id="devYear">
                @foreach($reportYears as $y)
                    <option value="{{ $y }}" @selected($y === $selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card bg-green-500 text-white">
            <p class="text-sm opacity-80">Active Developers</p>
            <p class="text-3xl font-bold">{{ $kpis['active_developers'] }}</p>
            <p class="text-xs opacity-80">with active listings</p>
        </div>

        <div class="card bg-blue-500 text-white">
            <p class="text-sm opacity-80">Active Listings</p>
            <p class="text-3xl font-bold">{{ $kpis['active_listings'] }}</p>
            <p class="text-xs opacity-80">developer inventory</p>
        </div>

        <div class="card bg-purple-500 text-white">
            <p class="text-sm opacity-80">Top Developer (Leads)</p>
            <p class="text-lg font-bold">{{ $kpis['top_by_leads'] }}</p>
            <p class="text-xs opacity-80">{{ $kpis['top_by_leads_count'] }} leads</p>
        </div>

        <div class="card bg-pink-500 text-white">
            <p class="text-sm opacity-80">Top Developer (Revenue)</p>
            <p class="text-lg font-bold">{{ $kpis['top_by_revenue'] }}</p>
            <p class="text-xs opacity-80">€{{ number_format($kpis['top_by_revenue_amount']) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <h3 class="card-title">Listings by Developer</h3>
                <span class="text-xs text-gray-400">inventory</span>
            </div>
            <canvas id="chartListingsByDev" height="120"></canvas>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <h3 class="card-title">Leads by Developer</h3>
                <span class="text-xs text-gray-400">engagement</span>
            </div>
            <canvas id="chartLeadsByDev" height="120"></canvas>
        </div>
    </div>

    {{-- Developer Table --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title">Developer Performance</h3>
            <span class="text-xs text-gray-400">dummy but structured for real DB later</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Developer</th>
                    <th>Projects</th>
                    <th>Active Listings</th>
                    <th>Leads</th>
                    <th>Avg Price</th>
                    <th>Regions</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($developersTable as $d)
                    <tr>
                        <td class="font-medium">{{ $d['name'] }}</td>
                        <td>{{ $d['projects'] }}</td>
                        <td>{{ $d['active_listings'] }}</td>
                        <td>{{ $d['leads'] }}</td>
                        <td>€{{ number_format($d['avg_price']) }}</td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @foreach($d['regions'] as $r)
                                    <span class="badge badge-light">{{ $r }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @php
                                $badge = match($d['status']) {
                                    'Active' => 'badge-success',
                                    'Low Stock' => 'badge-warning',
                                    'Stale' => 'badge-danger',
                                    default => 'badge-light'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $d['status'] }}</span>
                        </td>
                        <td>
                            <a href="{{ $d['url'] }}" class="btn btn-sm btn-outline">
                                View Listings
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
(function () {
    if (typeof Chart === 'undefined') return;

    const listings = @json($chartListingsByDeveloper ?? ['labels'=>[], 'values'=>[]]);
    const leads    = @json($chartLeadsByDeveloper ?? ['labels'=>[], 'values'=>[]]);

    const el1 = document.getElementById('chartListingsByDev');
    const el2 = document.getElementById('chartLeadsByDev');

    if (el1) {
        new Chart(el1, {
            type: 'bar',
            data: {
                labels: listings.labels,
                datasets: [{
                    label: 'Listings',
                    data: listings.values
                }]
            },
            options: { responsive: true }
        });
    }

    if (el2) {
        new Chart(el2, {
            type: 'bar',
            data: {
                labels: leads.labels,
                datasets: [{
                    label: 'Leads',
                    data: leads.values
                }]
            },
            options: { responsive: true }
        });
    }

})();
</script>
