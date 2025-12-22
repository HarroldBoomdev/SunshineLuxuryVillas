{{-- resources/views/report/partials/potential-buyers.blade.php --}}

@php
    $filtersJson = $filters ?? [
        'region' => 'all',
        'town'   => 'all',
        'type'   => 'all',
        'budget' => 'all',
        'status' => 'all',
    ];
@endphp

<div id="potentialBuyersContainer" class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Potential Buyers</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        Buyers with defined budgets, preferences, and matching properties.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-semibold bg-gray-50 border text-gray-700">
                        Sales Intelligence
                    </span>

                    <select id="buyersYear" class="border rounded-md px-3 py-2 text-sm">
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
                <select id="buyerRegion" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Regions</option>
                    @foreach(($regions ?? []) as $r)
                        <option value="{{ $r }}" @selected(($filters['region'] ?? 'all') === $r)>{{ $r }}</option>
                    @endforeach
                </select>

                <select id="buyerTown" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Towns</option>
                </select>

                <select id="buyerType" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Property Types</option>
                    @foreach(($propertyTypes ?? []) as $t)
                        <option value="{{ $t }}" @selected(($filters['type'] ?? 'all') === $t)>{{ $t }}</option>
                    @endforeach
                </select>

                <select id="buyerBudget" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Budgets</option>
                    <option value="0-250000">Up to €250k</option>
                    <option value="250000-400000">€250k – €400k</option>
                    <option value="400000-600000">€400k – €600k</option>
                    <option value="600000-1000000">€600k – €1M</option>
                    <option value="1000000+">€1M+</option>
                </select>

                <select id="buyerStatus" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Status</option>
                    @foreach(($buyerStatuses ?? []) as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? 'all') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end mt-3">
                <button id="applyBuyerFilters"
                        class="px-4 py-2 rounded-md text-sm font-semibold text-white"
                        style="background:#f59e0b;">
                    Apply Filters
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#22c55e;">
                <div class="text-sm font-semibold opacity-90">Active Potential Buyers</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['active_buyers'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">buyers</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#3b82f6;">
                <div class="text-sm font-semibold opacity-90">With Defined Budget</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['with_budget'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">ready</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#ec4899;">
                <div class="text-sm font-semibold opacity-90">With Matching Properties</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['with_matches'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">matched</div>
            </div>

            <div class="p-5 rounded-lg text-white shadow-sm" style="background:#111827;">
                <div class="text-sm font-semibold opacity-90">High-Intent Buyers</div>
                <div class="text-3xl font-extrabold mt-2">{{ $kpis['high_intent'] ?? 0 }}</div>
                <div class="text-xs opacity-90 mt-1">priority</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Buyer Readiness Funnel</h3>
                <canvas id="buyerFunnelChart" style="height:320px;"></canvas>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Budget Distribution</h3>
                <canvas id="buyerBudgetChart" style="height:320px;"></canvas>
            </div>

        </div>

        {{-- Potential Buyers Table --}}
        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">Potential Buyers List</h3>
                <span class="text-xs text-gray-500">actionable</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-3">Buyer</th>
                        <th class="py-2 pr-3">Budget</th>
                        <th class="py-2 pr-3">Region</th>
                        <th class="py-2 pr-3">Property Type</th>
                        <th class="py-2 pr-3 text-right">Matches</th>
                        <th class="py-2 pr-3">Suggested Properties</th>
                        <th class="py-2 pr-3">Status</th>
                        <th class="py-2 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse(($buyersTable ?? []) as $row)
                        <tr class="text-gray-700">
                            <td class="py-2 pr-3 font-medium">{{ $row['name'] }}</td>
                            <td class="py-2 pr-3 font-semibold">€{{ number_format($row['budget_min']) }}–€{{ number_format($row['budget_max']) }}</td>
                            <td class="py-2 pr-3">{{ $row['region'] }}</td>
                            <td class="py-2 pr-3">{{ $row['type'] }}</td>
                            <td class="py-2 pr-3 text-right font-semibold">{{ $row['matches'] }}</td>
                            <td class="py-2 pr-3">
                                <a href="{{ $row['suggested_url'] ?? '#' }}"
                                   class="text-blue-600 hover:underline">
                                    View ({{ $row['matches'] }})
                                </a>
                            </td>
                            <td class="py-2 pr-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="py-2 text-right">
                                <a href="#" class="text-indigo-600 hover:underline text-sm">Contact</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-6 text-center text-gray-500">No buyers found</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@php
    $buyerFunnelJson = $chartBuyerFunnel ?? [
        'labels' => [],
        'values' => [],
    ];

    $buyerBudgetJson = $chartBuyerBudget ?? [
        'labels' => [],
        'values' => [],
    ];
@endphp


<script>
(function () {
  if (typeof Chart === 'undefined') return;

  const townsByRegion = @json($townsByRegion ?? []);
  const filters = @json($filtersJson);

  const regionSel = document.getElementById('buyerRegion');
  const townSel   = document.getElementById('buyerTown');

  function setTownOptions(regionValue) {
    if (!townSel) return;

    townSel.innerHTML = '<option value="all">All Towns</option>';

    let towns = [];
    if (regionValue !== 'all' && townsByRegion[regionValue]) {
      towns = townsByRegion[regionValue];
    } else {
      Object.values(townsByRegion).forEach(list => towns = towns.concat(list));
    }

    towns = [...new Set(towns)].sort();

    towns.forEach(town => {
      const opt = document.createElement('option');
      opt.value = town;
      opt.textContent = town;
      townSel.appendChild(opt);
    });
  }

  if (regionSel) {
    setTownOptions(regionSel.value || 'all');
    regionSel.addEventListener('change', () => {
      setTownOptions(regionSel.value);
      if (townSel) townSel.value = 'all';
    });
  }

  // Charts (payloads from controller)
    const funnel  = @json($buyerFunnelJson);
    const budgets = @json($buyerBudgetJson);


  if (document.getElementById('buyerFunnelChart') && funnel.labels) {
    new Chart(document.getElementById('buyerFunnelChart'), {
      type: 'bar',
      data: { labels: funnel.labels, datasets: [{ data: funnel.values }] },
      options: { indexAxis: 'y', plugins: { legend: { display: false } } }
    });
  }

  if (document.getElementById('buyerBudgetChart') && budgets.labels) {
    new Chart(document.getElementById('buyerBudgetChart'), {
      type: 'bar',
      data: { labels: budgets.labels, datasets: [{ data: budgets.values }] },
      options: { plugins: { legend: { display: false } } }
    });
  }
})();
</script>
