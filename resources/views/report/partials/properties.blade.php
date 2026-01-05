@php
  $reportYears      = $reportYears      ?? [];
  $selectedYear     = $selectedYear     ?? now()->year;
  $propertyStats    = $propertyStats    ?? [];

  $totalListings    = (int)($totalListings ?? 0);
  $avgValue         = (float)($avgValue ?? 0);
  $listedThisMonth  = (int)($listedThisMonth ?? 0);

  $listingsPerDistrict   = $listingsPerDistrict   ?? [];
  $avgPricePerDistrict   = $avgPricePerDistrict   ?? [];
  $listingsPerType       = $listingsPerType       ?? [];
  $typeByDistrict        = $typeByDistrict        ?? [];
  $listingsPerPortal     = $listingsPerPortal     ?? [];

  $money = function($v){
      return '€' . number_format((float)($v ?? 0), 0, '.', ',');
  };
@endphp

<div id="propertiesContainer">
  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="p-6 bg-white rounded-lg shadow-md">

        {{-- Toolbar --}}
        <div class="mb-6 rounded-lg border border-gray-200 bg-white/90 p-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-800">Properties Report</h2>
            <p class="text-sm text-gray-500">Select a year to update the data.</p>
          </div>

          <div>
            <select id="propertiesYear" class="border border-gray-300 rounded px-3 py-2 text-sm w-40">
              @foreach($reportYears as $y)
                <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>
                  {{ $y }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <div class="p-4 rounded border text-center">
            <div class="text-sm text-gray-500">Total Listings</div>
            <div class="text-2xl font-bold">{{ number_format($totalListings) }}</div>
          </div>

          <div class="p-4 rounded border text-center">
            <div class="text-sm text-gray-500">Avg. Value</div>
            <div class="text-2xl font-bold">{{ $money($avgValue) }}</div>
          </div>

          <div class="p-4 rounded border text-center">
            <div class="text-sm text-gray-500">Listed This Month</div>
            <div class="text-2xl font-bold">{{ number_format($listedThisMonth) }}</div>
          </div>
        </div>

        {{-- Monthly Chart --}}
        <div class="bg-white p-6 rounded shadow mb-10">
          <h2 class="text-lg font-semibold mb-4">Monthly Listings ({{ $selectedYear }})</h2>
          <canvas id="chart-properties" height="80"></canvas>
        </div>

        {{-- Listings per District --}}
        <div class="bg-white p-6 rounded shadow mb-10">
          <h2 class="text-lg font-semibold mb-4">Listings & Avg Price by District</h2>
          <table class="min-w-full text-sm border">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">District</th>
                <th class="px-4 py-2 text-right">Listings</th>
                <th class="px-4 py-2 text-right">Avg Price</th>
              </tr>
            </thead>
            <tbody>
              @foreach($listingsPerDistrict as $district => $count)
                <tr class="border-t">
                  <td class="px-4 py-2">{{ $district }}</td>
                  <td class="px-4 py-2 text-right font-semibold">{{ number_format($count) }}</td>
                  <td class="px-4 py-2 text-right">{{ $money($avgPricePerDistrict[$district] ?? 0) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Listings per Property Type --}}
        <div class="bg-white p-6 rounded shadow mb-10">
          <h2 class="text-lg font-semibold mb-4">Listings by Property Type</h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($listingsPerType as $type => $count)
              <div class="p-4 border rounded text-center">
                <div class="text-sm text-gray-500">{{ $type }}</div>
                <div class="text-2xl font-bold">{{ number_format($count) }}</div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Property Type × District --}}
        <div class="bg-white p-6 rounded shadow mb-10">
          <h2 class="text-lg font-semibold mb-4">Property Types by District</h2>
          <table class="min-w-full text-sm border">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">District</th>
                @foreach(array_keys($listingsPerType) as $type)
                  <th class="px-4 py-2 text-right">{{ $type }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($typeByDistrict as $district => $types)
                <tr class="border-t">
                  <td class="px-4 py-2 font-medium">{{ $district }}</td>
                  @foreach(array_keys($listingsPerType) as $type)
                    <td class="px-4 py-2 text-right">
                      {{ number_format($types[$type] ?? 0) }}
                    </td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Listings per Portal --}}
        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-lg font-semibold mb-4">Listings by Portal</h2>
          <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($listingsPerPortal as $portal => $count)
              <div class="p-4 border rounded text-center">
                <div class="text-sm text-gray-500">{{ $portal }}</div>
                <div class="text-2xl font-bold">{{ number_format($count) }}</div>
              </div>
            @endforeach
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Chart JS --}}
<script>
(function () {
  if (typeof Chart === 'undefined') return;

  const stats = @json($propertyStats);
  const labels = stats.map(i => i.month);
  const data = stats.map(i => Number(i.total) || 0);

  const ctx = document.getElementById('chart-properties');
  if (!ctx) return;

  new Chart(ctx.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Listings',
        data
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } },
        x: { grid: { display: false } }
      }
    }
  });

  const yearSelect = document.getElementById('propertiesYear');
  if (yearSelect) {
    yearSelect.addEventListener('change', () => {
      fetch(`/report/partials/properties?year=${yearSelect.value}`)
        .then(r => r.text())
        .then(html => {
          const container = document.getElementById('report-content');
          container.innerHTML = html;
          container.querySelectorAll('script').forEach(old => {
            const s = document.createElement('script');
            s.textContent = old.textContent;
            old.replaceWith(s);
          });
        });
    });
  }
})();
</script>
