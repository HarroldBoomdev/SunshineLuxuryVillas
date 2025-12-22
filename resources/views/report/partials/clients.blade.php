{{-- resources/views/report/partials/clients.blade.php --}}

@php
    $filtersJson = $filters ?? [
        'type'   => 'all',
        'status' => 'all',
        'region' => 'all',
        'town'   => 'all',
        'source' => 'all',
    ];
@endphp

<div id="clientsContainer" class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Clients Report</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        Track client distribution, engagement, status, and deal activity.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-semibold bg-gray-50 border text-gray-700">
                        Data Comparison
                    </span>

                    <select id="clientsYear" class="border rounded-md px-3 py-2 text-sm">
                        @foreach(($reportYears ?? [2024, 2025]) as $y)
                            <option value="{{ $y }}" @selected(($selectedYear ?? date('Y')) == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="p-4 bg-white rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <select id="clientType" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Client Types</option>
                    @foreach(($clientTypes ?? []) as $t)
                        <option value="{{ $t }}" @selected(($filters['type'] ?? 'all') === $t)>{{ $t }}</option>
                    @endforeach
                </select>

                <select id="clientStatus" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Status</option>
                    @foreach(($clientStatuses ?? []) as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? 'all') === $s)>{{ $s }}</option>
                    @endforeach
                </select>

                <select id="clientRegion" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Regions</option>
                    @foreach(($regions ?? []) as $r)
                        <option value="{{ $r }}" @selected(($filters['region'] ?? 'all') === $r)>{{ $r }}</option>
                    @endforeach
                </select>

                <select id="clientTown" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Towns</option>
                    {{-- Populated by JS --}}
                </select>

                <select id="clientSource" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Sources</option>
                    @foreach(($clientSources ?? []) as $src)
                        <option value="{{ $src }}" @selected(($filters['source'] ?? 'all') === $src)>{{ $src }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end mt-3">
                <button id="applyClientFilters"
                        class="px-4 py-2 rounded-md text-sm font-semibold text-white"
                        style="background:#f59e0b;">
                    Apply Filters
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#22c55e;">
                <div class="text-sm font-semibold opacity-90">Total Active Clients</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['active_clients'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">filtered</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#3b82f6;">
                <div class="text-sm font-semibold opacity-90">Buyers vs Sellers</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['buyers'] ?? 0 }} / {{ $kpis['sellers'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">buyers / sellers</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#ec4899;">
                <div class="text-sm font-semibold opacity-90">Repeat Clients</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['repeat_clients'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">returning</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#111827;">
                <div class="text-sm font-semibold opacity-90">Clients With Open Deals</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['open_deals_clients'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">pipeline</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">Client Types Distribution</h3>
                    <span class="text-xs text-gray-500">buyers/sellers/investors…</span>
                </div>
                <canvas id="clientTypeChart" style="height:320px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">Client Status Overview</h3>
                    <span class="text-xs text-gray-500">active/negotiation/inactive…</span>
                </div>
                <canvas id="clientStatusChart" style="height:320px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">Clients by Region</h3>
                    <span class="text-xs text-gray-500">stacked buyers/sellers</span>
                </div>
                <canvas id="clientsByRegionChart" style="height:320px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">Clients by Source</h3>
                    <span class="text-xs text-gray-500">website/referral/portal…</span>
                </div>
                <canvas id="clientSourceChart" style="height:320px;"></canvas>
            </div>
        </div>

        {{-- Clients Table --}}
        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">Clients List</h3>
                <span class="text-xs text-gray-500">dummy</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-3">Client</th>
                        <th class="py-2 pr-3">Type</th>
                        <th class="py-2 pr-3">Region</th>
                        <th class="py-2 pr-3">Town</th>
                        <th class="py-2 pr-3">Status</th>
                        <th class="py-2 pr-3 text-right">Deals</th>
                        <th class="py-2 pr-3">Source</th>
                        <th class="py-2 text-right">Last Activity</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse(($clientsTable ?? []) as $row)
                        <tr class="text-gray-700">
                            <td class="py-2 pr-3 font-medium">{{ $row['name'] }}</td>
                            <td class="py-2 pr-3">{{ $row['type'] }}</td>
                            <td class="py-2 pr-3">{{ $row['region'] }}</td>
                            <td class="py-2 pr-3">{{ $row['town'] }}</td>
                            <td class="py-2 pr-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="py-2 pr-3 text-right font-semibold">{{ $row['deals'] }}</td>
                            <td class="py-2 pr-3">{{ $row['source'] }}</td>
                            <td class="py-2 text-right">{{ $row['last_activity'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-6 text-center text-gray-500">No data</td></tr>
                    @endforelse
                    </tbody>
                </table>
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

  const townsByRegion = @json($townsByRegion ?? []);
  const filters = @json($filtersJson);

  // Dropdown refs
  const regionSel = document.getElementById('clientRegion');
  const townSel   = document.getElementById('clientTown');

  // Populate towns based on region (simple readable version)
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

    // restore selected
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
  } else {
    setTownOptions('all');
  }

  // Charts payloads (controller will supply dummy arrays)
  const chartType   = @json($chartClientTypes ?? []);
  const chartStatus = @json($chartClientStatus ?? []);
  const chartRegion = @json($chartClientsByRegion ?? []);
  const chartSource = @json($chartClientSources ?? []);

  function doughnut(canvasId, payload) {
    const el = document.getElementById(canvasId);
    if (!el || !payload.labels) return;

    new Chart(el.getContext('2d'), {
      type: 'doughnut',
      data: { labels: payload.labels, datasets: [{ data: payload.values || [] }] },
      options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
    });
  }

  function stackedBar(canvasId, payload) {
    const el = document.getElementById(canvasId);
    if (!el || !payload.labels) return;

    new Chart(el.getContext('2d'), {
      type: 'bar',
      data: {
        labels: payload.labels,
        datasets: payload.datasets || []
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } },
        plugins: { legend: { position: 'top' } }
      }
    });
  }

  doughnut('clientTypeChart', chartType);
  doughnut('clientStatusChart', chartStatus);
  stackedBar('clientsByRegionChart', chartRegion);
  doughnut('clientSourceChart', chartSource);

  // Apply filters -> use same AJAX pattern as other reports (adjust route if needed)
  const btn = document.getElementById('applyClientFilters');
  if (btn) {
    btn.addEventListener('click', () => {
      const year   = document.getElementById('clientsYear')?.value || '';
      const type   = document.getElementById('clientType')?.value || 'all';
      const status = document.getElementById('clientStatus')?.value || 'all';
      const region = document.getElementById('clientRegion')?.value || 'all';
      const town   = document.getElementById('clientTown')?.value || 'all';
      const source = document.getElementById('clientSource')?.value || 'all';

      const url = `/report/partials/clients?year=${encodeURIComponent(year)}&type=${encodeURIComponent(type)}&status=${encodeURIComponent(status)}&region=${encodeURIComponent(region)}&town=${encodeURIComponent(town)}&source=${encodeURIComponent(source)}`;

      const container = document.getElementById('report-content');
      if (!container) { window.location.href = url; return; }

      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
          const wrap = document.createElement('div');
          wrap.innerHTML = html;
          container.replaceChildren(...wrap.childNodes);

          // Re-run scripts inside reloaded partial
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
})();
</script>
