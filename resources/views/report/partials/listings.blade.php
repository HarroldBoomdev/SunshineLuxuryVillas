{{-- resources/views/report/partials/listings.blade.php --}}
<div id="listingsContainer" class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Listings Report</h2>
                    <p class="text-gray-500 text-sm mt-1">Inventory health, distribution and latest listing activity.</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-semibold bg-gray-50 border text-gray-700">
                        Data Comparison
                    </span>

                    <select id="reportYear" class="border rounded-md px-3 py-2 text-sm">
                        @foreach(($reportYears ?? [2024, 2025]) as $y)
                            <option value="{{ $y }}" @selected(($selectedYear ?? date('Y')) == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#22c55e;">
                <div class="text-sm font-semibold opacity-90">Active Listings</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['active'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">current inventory</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#f59e0b;">
                <div class="text-sm font-semibold opacity-90">New Listings (This Month)</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['new_this_month'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">added recently</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#3b82f6;">
                <div class="text-sm font-semibold opacity-90">Removed / Sold (This Month)</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['removed_this_month'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">cleanup / sold</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#ec4899;">
                <div class="text-sm font-semibold opacity-90">Avg Days on Market</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['avg_dom'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">dummy metric</div>
            </div>
        </div>

        {{-- Inventory Health --}}
        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">Property Listing</h3>
                <span class="text-xs text-gray-500">Data quality / publishing coverage (dummy)</span>
            </div>

            @php
                $den = max((int)($totalProperties ?? 0), 1);

                $rows = [
                    ['SLV',       (int)($portalSlv ?? 0)],
                    ['Rightmove', (int)($portalRightmove ?? 0)],
                    ['APITS',     (int)($portalApits ?? 0)],
                    ['Zoopla',    (int)($portalZoopla ?? 0)],
                    ['HoS',       (int)($portalHos ?? 0)],
                ];
            @endphp

            <div class="space-y-4">
                @foreach($portalCoverage as $p)
                    @php
                        $pct = $p['total'] > 0 ? round(($p['done'] / $p['total']) * 100) : 0;
                    @endphp

                    <div class="mb-3">
                        <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold">{{ $p['label'] }}</span>
                        <span>{{ number_format($p['done']) }}/{{ number_format($p['total']) }} ({{ $pct }}%)</span>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Listings by Region</h3>
                <canvas id="pieRegion" style="height:260px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Listings by Property Type</h3>
                <canvas id="pieType" style="height:260px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Listings by Status</h3>
                <canvas id="pieStatus" style="height:260px;"></canvas>
            </div>
        </div>

        {{-- Tables --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Latest Listings --}}
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Latest Listings Added</h3>
                    <span class="text-xs text-gray-500">Top 10 (dummy)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">Ref</th>
                            <th class="py-2 pr-3">Town</th>
                            <th class="py-2 pr-3">Type</th>
                            <th class="py-2 pr-3 text-right">Price</th>
                            <th class="py-2 pr-3 text-right">Beds</th>
                            <th class="py-2 pr-3">Agent</th>
                            <th class="py-2">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @forelse(($latestListings ?? []) as $row)
                            <tr class="text-gray-700">
                                <td class="py-2 pr-3 font-medium">{{ $row['ref'] }}</td>
                                <td class="py-2 pr-3">{{ $row['town'] }}</td>
                                <td class="py-2 pr-3">{{ $row['type'] }}</td>
                                <td class="py-2 pr-3 text-right font-semibold">€{{ number_format($row['price']) }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['beds'] }}</td>
                                <td class="py-2 pr-3">{{ $row['agent'] }}</td>
                                <td class="py-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-gray-500">No data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Region Summary --}}
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Region Summary</h3>
                    <span class="text-xs text-gray-500">Distribution & Avg Price (dummy)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">Region</th>
                            <th class="py-2 pr-3 text-right">Listings</th>
                            <th class="py-2 pr-3 text-right">Avg Price</th>
                            <th class="py-2 text-right">% Share</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @php
                            $totalListings = collect($regionSummary ?? [])->sum('count') ?: 1;
                        @endphp

                        @forelse(($regionSummary ?? []) as $row)
                            @php
                                $pct = round(($row['count'] / $totalListings) * 100, 1);
                            @endphp
                            <tr class="text-gray-700">
                                <td class="py-2 pr-3 font-medium">{{ $row['region'] }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['count'] }}</td>
                                <td class="py-2 pr-3 text-right">€{{ number_format($row['avg_price']) }}</td>
                                <td class="py-2 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">No data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded.');
    return;
  }

  const byRegion = @json($byRegion ?? []);
  const byType   = @json($byType ?? []);
  const byStatus = @json($byStatus ?? []);

  function randColor() {
    const r = 150 + Math.floor(Math.random()*105);
    const g = 150 + Math.floor(Math.random()*105);
    const b = 150 + Math.floor(Math.random()*105);
    return `rgb(${r},${g},${b})`;
  }

  function makePie(canvasId, obj) {
    const labels = Object.keys(obj);
    const data   = labels.map(k => Number(obj[k]) || 0);
    const canvas = document.getElementById(canvasId);
    if (!canvas || labels.length === 0) return;

    new Chart(canvas.getContext('2d'), {
      type: 'pie',
      data: { labels, datasets: [{ data, backgroundColor: labels.map(() => randColor()) }] },
      options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
    });
  }

  makePie('pieRegion', byRegion);
  makePie('pieType', byType);
  makePie('pieStatus', byStatus);

})();
</script>
