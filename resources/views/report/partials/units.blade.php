{{-- resources/views/report/partials/units.blade.php --}}
<div id="unitsContainer" class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Units Report</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        Resale vs Brand New comparison by Region, Town and Property Type.
                    </p>
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

        {{-- Filters (like your website search bar) --}}
        <div class="p-4 bg-white rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select id="filterStatus" class="border rounded-md px-3 py-2 text-sm">
                    @foreach(($statusOptions ?? []) as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['status'] ?? 'all') === $key)>{{ $label }}</option>
                    @endforeach
                </select>

                <select id="filterRegion" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Regions</option>
                    @foreach(($regions ?? []) as $r)
                        <option value="{{ $r }}" @selected(($filters['region'] ?? 'all') === $r)>{{ $r }}</option>
                    @endforeach
                </select>

                <select id="filterTown" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Towns</option>
                    {{-- Town options populated by JS from townsByRegion --}}
                </select>

                <select id="filterType" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Property Types</option>
                    @foreach(($propertyTypes ?? []) as $t)
                        <option value="{{ $t }}" @selected(($filters['type'] ?? 'all') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end mt-3">
                <button id="applyUnitsFilters"
                        class="px-4 py-2 rounded-md text-sm font-semibold text-white"
                        style="background:#f59e0b;">
                    Apply Filters
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#22c55e;">
                <div class="text-sm font-semibold opacity-90">Total Units</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['total'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">filtered result</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#3b82f6;">
                <div class="text-sm font-semibold opacity-90">Resale Units</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['resale'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">secondary market</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#ec4899;">
                <div class="text-sm font-semibold opacity-90">Brand New Units</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['brand_new'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">new development</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#111827;">
                <div class="text-sm font-semibold opacity-90">Resale vs Brand New</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['split'] ?? '0% / 0%' }}</div>
                <div class="text-xs opacity-90 mt-1">resale / brand new</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 gap-6 mb-6">
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">Resale vs Brand New by Property Type</h3>
                    <span class="text-xs text-gray-500">stacked comparison</span>
                </div>
                <canvas id="stackType" style="height:320px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">Resale vs Brand New by Region</h3>
                    <span class="text-xs text-gray-500">stacked comparison</span>
                </div>
                <canvas id="stackRegion" style="height:320px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">Resale vs Brand New by Town</h3>
                    <span class="text-xs text-gray-500">top towns based on selected region</span>
                </div>
                <canvas id="stackTown" style="height:340px;"></canvas>
            </div>
        </div>

        {{-- Tables --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Type Summary --}}
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Property Type Summary</h3>
                    <span class="text-xs text-gray-500">dummy</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">Type</th>
                            <th class="py-2 pr-3 text-right">Resale</th>
                            <th class="py-2 pr-3 text-right">Brand New</th>
                            <th class="py-2 pr-3 text-right">Total</th>
                            <th class="py-2 pr-3 text-right">Avg Price</th>
                            <th class="py-2 text-right">% Share</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @php $typeTotal = collect($typeSummary ?? [])->sum('total') ?: 1; @endphp
                        @forelse(($typeSummary ?? []) as $row)
                            @php $pct = round(($row['total'] / $typeTotal) * 100, 1); @endphp
                            <tr class="text-gray-700">
                                <td class="py-2 pr-3 font-medium">{{ $row['type'] }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['resale'] }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['brand_new'] }}</td>
                                <td class="py-2 pr-3 text-right font-semibold">{{ $row['total'] }}</td>
                                <td class="py-2 pr-3 text-right">€{{ number_format($row['avg_price']) }}</td>
                                <td class="py-2 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Region/Town Summary --}}
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Region / Town Summary</h3>
                    <span class="text-xs text-gray-500">dummy</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">Area</th>
                            <th class="py-2 pr-3 text-right">Resale</th>
                            <th class="py-2 pr-3 text-right">Brand New</th>
                            <th class="py-2 pr-3 text-right">Total</th>
                            <th class="py-2 pr-3 text-right">Avg Price</th>
                            <th class="py-2 text-right">% Share</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @php $areaTotal = collect($areaSummary ?? [])->sum('total') ?: 1; @endphp
                        @forelse(($areaSummary ?? []) as $row)
                            @php $pct = round(($row['total'] / $areaTotal) * 100, 1); @endphp
                            <tr class="text-gray-700">
                                <td class="py-2 pr-3 font-medium">{{ $row['area'] }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['resale'] }}</td>
                                <td class="py-2 pr-3 text-right">{{ $row['brand_new'] }}</td>
                                <td class="py-2 pr-3 text-right font-semibold">{{ $row['total'] }}</td>
                                <td class="py-2 pr-3 text-right">€{{ number_format($row['avg_price']) }}</td>
                                <td class="py-2 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@php
    $filtersJson = $filters ?? [
        'status' => 'all',
        'region' => 'all',
        'town'   => 'all',
        'type'   => 'all',
    ];
@endphp


<script>
(function () {
  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded.');
    return;
  }

  // Dummy datasets provided by controller
  const townsByRegion = @json($townsByRegion ?? []);
  const filters = @json($filtersJson);

  const chartByType   = @json($chartByType ?? []);
  const chartByRegion = @json($chartByRegion ?? []);
  const chartByTown   = @json($chartByTown ?? []);

  // ---------------------------
  // Populate towns dropdown
  // ---------------------------
  const regionSel = document.getElementById('filterRegion');
  const townSel   = document.getElementById('filterTown');

  function setTownOptions(regionValue) {
    if (!townSel) return;

    townSel.innerHTML = '<option value="all">All Towns</option>';

    let towns = [];

    if (regionValue !== 'all' && townsByRegion[regionValue]) {
        towns = townsByRegion[regionValue];
    } else {
        Object.values(townsByRegion).forEach(regionTowns => {
        towns = towns.concat(regionTowns);
        });
    }

    towns = [...new Set(towns)].sort();

    towns.forEach(town => {
        const opt = document.createElement('option');
        opt.value = town;
        opt.textContent = town;
        townSel.appendChild(opt);
    });

    // restore selected town (important for reload / ajax)
    if (filters.town && [...townSel.options].some(o => o.value === filters.town)) {
        townSel.value = filters.town;
    }
    }

    if (regionSel) {
    setTownOptions(regionSel.value || 'all');

    regionSel.addEventListener('change', () => {
        setTownOptions(regionSel.value);
        if (townSel) townSel.value = 'all';
    });
    }


  // ---------------------------
  // Apply filter button -> reload partial
  // ---------------------------
  const btnApply = document.getElementById('applyUnitsFilters');
  if (btnApply) {
    btnApply.addEventListener('click', () => {
      const status = document.getElementById('filterStatus')?.value || 'all';
      const region = document.getElementById('filterRegion')?.value || 'all';
      const town   = document.getElementById('filterTown')?.value || 'all';
      const type   = document.getElementById('filterType')?.value || 'all';
      const year   = document.getElementById('reportYear')?.value || '';

      const url = `/report/partials/units?year=${encodeURIComponent(year)}&status=${encodeURIComponent(status)}&region=${encodeURIComponent(region)}&town=${encodeURIComponent(town)}&type=${encodeURIComponent(type)}`;

      const container = document.getElementById('report-content');
      if (!container) {
        window.location.href = url; // fallback
        return;
      }

      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
          const wrap = document.createElement('div');
          wrap.innerHTML = html;
          container.replaceChildren(...wrap.childNodes);

          // Re-run scripts inside the reloaded partial
          container.querySelectorAll('script').forEach(old => {
            const s = document.createElement('script');
            [...old.attributes].forEach(a => s.setAttribute(a.name, a.value));
            s.textContent = old.textContent;
            old.replaceWith(s);
          });
        })
        .catch(() => window.location.href = url);
    });
  }

  // ---------------------------
  // Charts: stacked bars
  // ---------------------------
  function buildStacked(canvasId, payload) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !payload || !payload.labels) return;

    new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: payload.labels,
        datasets: [
          { label: 'Resale', data: payload.resale || [] },
          { label: 'Brand New', data: payload.brand_new || [] },
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          x: { stacked: true, grid: { display: false } },
          y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
        },
        plugins: { legend: { position: 'top' } }
      }
    });
  }

  buildStacked('stackType', chartByType);
  buildStacked('stackRegion', chartByRegion);
  buildStacked('stackTown', chartByTown);

})();
</script>
