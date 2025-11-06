<style>
  /* keep charts tidy across envs */
  .chart-fixed { width:100%; max-height:420px; }
  @media (min-width:1280px){ .chart-fixed{ max-height:380px; } }
</style>

<div class="py-8">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="p-6 bg-white rounded-lg shadow-md">

      <!-- Top KPIs -->
      <div class="grid grid-cols-4 gap-4 text-center mb-8">
        <div class="bg-green-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Total Sales (This Year)</h3>
          <p class="text-2xl font-semibold">{{ $salesThisYear }}</p>
        </div>
        <div class="bg-yellow-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Total Leads (2022–2024)</h3>
          <p class="text-2xl font-semibold">{{ $leadStats->sum('total') }}</p>
        </div>
        <div class="bg-blue-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Quarter Target</h3>
          <p class="text-2xl font-semibold">{{ $salesThisQuarter }}/{{ $quarterTarget }}</p>
        </div>
        <div class="bg-pink-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Year Target Progress</h3>
          <p class="text-2xl font-semibold">{{ round(($salesThisYear / $yearlyTarget) * 100, 1) }}%</p>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="mb-6">
        <h4 class="text-md font-semibold mb-1">Yearly Sales Target</h4>
        <div class="w-full bg-gray-300 rounded-full h-4">
          <div class="bg-green-600 h-4 rounded-full" style="width: {{ min(100, ($salesThisYear / $yearlyTarget) * 100) }}%"></div>
        </div>
      </div>

      <!-- Charts: Leads & Sales -->
      <div class="grid grid-cols-2 gap-4">
        <div class="p-4 bg-gray-100 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold mb-4">Leads by Month</h3>
          <canvas id="leadChart" class="chart-fixed"></canvas>
        </div>
        <div class="p-4 bg-gray-100 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold mb-4">Sales by Month</h3>
          <canvas id="salesChart" class="chart-fixed"></canvas>
        </div>
      </div>

      <!-- Two Pie Charts (Jan–Jun and Jul–Dec) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <div class="p-4 bg-gray-100 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold mb-4">Sales (Jan–Jun)</h3>
          <canvas id="sourcePieH1" class="chart-fixed"></canvas>
        </div>
        <div class="p-4 bg-gray-100 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold mb-4">Sales (Jul–Dec)</h3>
          <canvas id="sourcePieH2" class="chart-fixed"></canvas>
        </div>
      </div>

      <!-- Comparison Bar Chart -->
      <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold mb-4">Sales by Source Comparison (Jan–Jun vs Jul–Dec)</h3>
        <canvas id="sourceCompareChart" class="chart-fixed"></canvas>
      </div>

    </div>
  </div>
</div>

@php
  // Fallbacks so Blade never throws "Undefined variable"
  $sourcesH1 = $sourcesH1 ?? ['Rightmove'=>10,'APITS'=>9,'Zoopla'=>3,'SLV'=>5,'HoS'=>2]; // Jan–Jun
  $sourcesH2 = $sourcesH2 ?? ['Rightmove'=>3,'APITS'=>5,'Zoopla'=>5,'SLV'=>4,'HoS'=>0]; // Jul–Dec
@endphp

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  /* ===== Existing monthly charts ===== */
  const leadStats  = @json($leadStats);
  const salesStats = @json($salesStats);

  const months = [...new Set(leadStats.map(i => i.month))];
  const leadCounts  = months.map(m => leadStats.find(i => i.month === m)?.total ?? 0);
  const salesCounts = months.map(m => salesStats.find(i => i.month === m)?.total ?? 0);

  new Chart(document.getElementById('leadChart'), {
    type: 'bar',
    data: { labels: months, datasets: [{ label: 'Leads', data: leadCounts, backgroundColor: '#34d399' }] },
    options: { responsive: true, maintainAspectRatio: true }
  });

  new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Sales',
        data: salesCounts,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.2)',
        borderWidth: 2, fill: true, tension: 0.3
      }]
    },
    options: { responsive: true, maintainAspectRatio: true }
  });

  /* ===== Pies + comparison bar ===== */
  const providedH1 = @json($sourcesH1);
  const providedH2 = @json($sourcesH2);
  const pieColors = ['#5AA6F8','#F6F062','#E86AF7','#66E08C','#F45A5A'];

  function renderPie(canvasId, sourceObj) {
    const labels = Object.keys(sourceObj);
    const data   = Object.values(sourceObj);
    const total  = data.reduce((a,b)=>a+b,0);
    return new Chart(document.getElementById(canvasId), {
      type: 'pie',
      data: { labels, datasets: [{ data, backgroundColor: pieColors, borderColor:'#fff', borderWidth:2 }] },
      options: {
        responsive: true, maintainAspectRatio: true,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: { callbacks: { label: (ctx) => {
            const v = ctx.raw ?? 0, p = total ? (v/total*100).toFixed(1) : 0;
            return `${ctx.label}: ${v} (${p}%)`;
          }}}
        }
      }
    });
  }
  renderPie('sourcePieH1', providedH1);
  renderPie('sourcePieH2', providedH2);

  // Grouped bar comparison
  new Chart(document.getElementById('sourceCompareChart'), {
    type: 'bar',
    data: {
      labels: Object.keys(providedH1),
      datasets: [
        { label: 'Jan–Jun', data: Object.values(providedH1), backgroundColor: 'rgba(99,179,237,0.8)' },
        { label: 'Jul–Dec', data: Object.values(providedH2), backgroundColor: 'rgba(244,114,182,0.8)' }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: true,
      plugins: { legend: { position: 'bottom' }, tooltip: { mode: 'index', intersect: false } },
      scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
    }
  });
</script>
