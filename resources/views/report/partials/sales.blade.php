<style>
  .chart-fixed { width:100%; max-height:420px; }
  @media (min-width:1280px){ .chart-fixed{ max-height:380px; } }
  .qbtn { padding: 6px 14px; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; border: 1.5px solid #d1d5db; background: #fff; color: #374151; transition: all 0.15s ease; }
  .qbtn:hover { background: #f3f4f6; }
  .qbtn.active { background: #2563eb; border-color: #2563eb; color: #fff; box-shadow: 0 2px 4px rgba(37,99,235,0.25); }
</style>

@php
  // Defensive defaults (prevents 500s if controller ever misses something)
  $reportYears      = $reportYears      ?? [];
  $selectedYear     = $selectedYear     ?? now()->year;
  $leadStats        = $leadStats        ?? [];
  $salesThisYear    = (int)($salesThisYear    ?? 0);
  $salesThisQuarter = (int)($salesThisQuarter ?? 0);
  $quarterTarget    = (int)($quarterTarget    ?? 0);
  $yearlyTarget     = (int)($yearlyTarget     ?? 1);

  $allLeadsTotal    = (int)($allLeadsTotal    ?? 0);
  $allLeadsAvg      = (int)($allLeadsAvg      ?? 0);
  $leadsByLocation  = $leadsByLocation  ?? [];
  $avgByLocation    = $avgByLocation    ?? [];
  $leadsBySource    = $leadsBySource    ?? [];

  // Calculate total for the visible “Total Leads (Year)” card safely
  $totalLeadsSelectedYear = is_array($leadStats)
      ? array_sum(array_column($leadStats, 'total'))
      : 0;

  $dc = $comparisonData ?? null;
@endphp

{{-- =========================================================
   TAB 1: Sales Dashboard (existing content)
   ========================================================= --}}
<div id="salesDashboardTab">
  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="p-6 bg-white rounded-lg shadow-md">

        {{-- Toolbar --}}
        <div class="mb-6 rounded-lg border border-gray-200 bg-white/90 p-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-800">Sales Report</h2>
            <p class="text-sm text-gray-500">Use the filters to change what’s shown below.</p>
          </div>

          <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col">
              <button id="openSalesComparison" class="qbtn">Data Comparison</button>

              <select id="reportYear" class="border border-gray-300 rounded px-3 py-2 text-sm w-40 md:w-52">
                @foreach($reportYears as $y)
                  <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        @php
            $money = function($v){
                return '€' . number_format((float)($v ?? 0), 0, '.', ',');
            };

            $kpiAreas = $kpiAreas ?? [
                'Paphos' => 0,
                'Limassol' => 0,
                'Famagusta' => 0,
                'Pissouri' => 0,
                'Larnaca' => 0,
            ];

            // Optional: colors per card (match your existing style)
            $areaColors = [
                'Paphos'    => '#22c55e', // green
                'Limassol'  => '#eab308', // yellow
                'Famagusta' => '#3b82f6', // blue
                'Pissouri'  => '#ec4899', // pink
                'Larnaca'   => '#8b5cf6', // purple
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
            @foreach($kpiAreas as $area => $value)
                <div class="rounded-xl shadow-md p-8 text-center text-white"
                    style="background: {{ $areaColors[$area] ?? '#16a34a' }};">
                    <div class="text-lg font-semibold">{{ $area }}</div>
                    <div class="text-3xl font-bold mt-2">{{ $money($value) }}</div>
                </div>
            @endforeach
        </div>


        @php
            $money = $money ?? function($v){
                return '€' . number_format((float)($v ?? 0), 0, '.', ',');
            };
        @endphp

        <div class="mb-8">
            <div class="font-semibold text-gray-800 mb-2">Total Sales</div>

            <div class="h-4 rounded-full bg-gray-200 overflow-hidden">
                <div class="h-4 bg-green-600 w-full"></div>
            </div>

            <div class="text-gray-800 mt-2">{{ $money($totalSalesMoney ?? 0) }}</div>
        </div>


        {{-- Monthly Leads chart (existing interactive block) --}}
        <div class="p-4 bg-gray-100 rounded-lg shadow-md">
          <div class="flex flex-wrap items-center justify-between gap-3 mb-4 border-b pb-2">
            <h3 id="leadsTitle" class="text-xl font-semibold text-gray-800">Leads January – December</h3>
            <div id="leadsControls" class="flex flex-wrap gap-2">
              <button class="qbtn active" data-view="all">All</button>
              <button class="qbtn" data-view="q1">Q1</button>
              <button class="qbtn" data-view="q2">Q2</button>
              <button class="qbtn" data-view="q3">Q3</button>
              <button class="qbtn" data-view="q4">Q4</button>
              <button class="qbtn" data-view="compare">Compare</button>
            </div>
          </div>

          <canvas id="leadsBar" class="chart-fixed"></canvas>

          <div id="leadsCompareGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4" style="display:none;">
            <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q1 (Jan–Mar)</h4><canvas id="barQ1" class="chart-fixed"></canvas></div>
            <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q2 (Apr–Jun)</h4><canvas id="barQ2" class="chart-fixed"></canvas></div>
            <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q3 (Jul–Sep)</h4><canvas id="barQ3" class="chart-fixed"></canvas></div>
            <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q4 (Oct–Dec)</h4><canvas id="barQ4" class="chart-fixed"></canvas></div>
          </div>
        </div>

        {{-- Tables: By Location / By Source --}}
        <div class="p-4 bg-white rounded-lg border border-gray-200 mt-6">
          <h3 class="text-md font-semibold mb-3">Breakdown ({{ $selectedYear }})</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-semibold mb-2 text-gray-700">By Location</h4>
              <table class="w-full text-sm border-collapse">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="text-left p-2 border">Location</th>
                    <th class="text-right p-2 border">Total</th>
                    <th class="text-right p-2 border">Avg/Month</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach(collect($leadsByLocation)->sortKeys() as $loc => $tot)
                    <tr>
                      <td class="p-2 border">{{ $loc ?: 'Unknown' }}</td>
                      <td class="p-2 border text-right">{{ (int)$tot }}</td>
                      <td class="p-2 border text-right">{{ (int)($avgByLocation[$loc] ?? 0) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div>
              <h4 class="text-sm font-semibold mb-2 text-gray-700">By Source</h4>
              <table class="w-full text-sm border-collapse">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="text-left p-2 border">Source</th>
                    <th class="text-right p-2 border">Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach(collect($leadsBySource)->sortKeys() as $src => $tot)
                    <tr>
                      <td class="p-2 border">{{ $src ?: 'Unknown' }}</td>
                      <td class="p-2 border text-right">{{ (int)$tot }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Visual Breakdown (Pie charts) --}}
        <div class="p-4 bg-white rounded-lg border border-gray-200 mt-6">
          <h3 class="text-md font-semibold mb-3">Visual Breakdown ({{ $selectedYear }})</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-semibold mb-2 text-gray-700">Leads by Location</h4>
              <canvas id="pieLocation" class="chart-fixed"></canvas>
            </div>
            <div>
              <h4 class="text-sm font-semibold mb-2 text-gray-700">Leads by Source</h4>
              <canvas id="pieSource" class="chart-fixed"></canvas>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- =========================================================
   TAB 2: Sales Comparison (replaces the whole dashboard)
   ========================================================= --}}
<div id="salesComparisonTab" class="hidden mt-8">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow p-5">

      <div class="flex items-start justify-between gap-4">
        <div>
          <h2 class="text-lg font-semibold">Sales Comparison</h2>
          <p class="text-sm text-gray-500">Compare 2 years for leads + sales breakdown charts.</p>
        </div>

        <div class="flex items-center gap-3">
          <div>
            <label class="text-xs text-gray-500">Year A</label>
            <select id="dcYearA" class="border rounded-lg px-3 py-2 text-sm">
              @foreach(($reportYears ?? []) as $y)
                <option value="{{ $y }}" {{ (int)($selectedYear ?? now()->year) === (int)$y ? 'selected' : '' }}>
                  {{ $y }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="text-xs text-gray-500">Year B</label>
            <select id="dcYearB" class="border rounded-lg px-3 py-2 text-sm">
              @php
                $sy = (int)($selectedYear ?? now()->year);
                $defaultB = $sy - 1;
              @endphp
              @foreach(($reportYears ?? []) as $y)
                <option value="{{ $y }}" {{ $defaultB === (int)$y ? 'selected' : '' }}>
                  {{ $y }}
                </option>
              @endforeach
            </select>
          </div>

          <button id="dcApply" type="button" class="border rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-50">
            Apply
          </button>

          <button id="backToSalesDashboard" type="button" class="border rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-50">
            Back
          </button>
        </div>
      </div>

      <hr class="my-5">

      @if(empty($dc))
        <div class="text-sm text-gray-600">
          No comparison data yet. (We’ll wire this once the controller returns <code>$comparisonData</code>.)
        </div>
      @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

          <div class="bg-gray-50 rounded-xl p-4">
            <h3 class="font-semibold mb-2">Leads (Monthly) — Year A vs Year B</h3>
            <canvas id="dcLeadsLine" height="140"></canvas>
          </div>

          <div class="bg-gray-50 rounded-xl p-4">
            <h3 class="font-semibold mb-2">Sales by Portal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><div class="text-xs text-gray-500 mb-1">Year A</div><canvas id="dcSalesPortalA" height="180"></canvas></div>
              <div><div class="text-xs text-gray-500 mb-1">Year B</div><canvas id="dcSalesPortalB" height="180"></canvas></div>
            </div>
          </div>

          <div class="bg-gray-50 rounded-xl p-4">
            <h3 class="font-semibold mb-2">Commission by Portal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><div class="text-xs text-gray-500 mb-1">Year A</div><canvas id="dcCommissionPortalA" height="180"></canvas></div>
              <div><div class="text-xs text-gray-500 mb-1">Year B</div><canvas id="dcCommissionPortalB" height="180"></canvas></div>
            </div>
          </div>

          <div class="bg-gray-50 rounded-xl p-4">
            <h3 class="font-semibold mb-2">Sales by Source</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><div class="text-xs text-gray-500 mb-1">Year A</div><canvas id="dcSalesSourceA" height="180"></canvas></div>
              <div><div class="text-xs text-gray-500 mb-1">Year B</div><canvas id="dcSalesSourceB" height="180"></canvas></div>
            </div>
          </div>

          <div class="bg-gray-50 rounded-xl p-4">
            <h3 class="font-semibold mb-2">Commission by Source</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><div class="text-xs text-gray-500 mb-1">Year A</div><canvas id="dcCommissionSourceA" height="180"></canvas></div>
              <div><div class="text-xs text-gray-500 mb-1">Year B</div><canvas id="dcCommissionSourceB" height="180"></canvas></div>
            </div>
          </div>

          <div class="bg-gray-50 rounded-xl p-4">
            <h3 class="font-semibold mb-2">Sales by Nationality</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><div class="text-xs text-gray-500 mb-1">Year A</div><canvas id="dcNationalityA" height="180"></canvas></div>
              <div><div class="text-xs text-gray-500 mb-1">Year B</div><canvas id="dcNationalityB" height="180"></canvas></div>
            </div>
          </div>

        </div>

        <script>
          (function(){
            const comparisonData = @json($comparisonData ?? []);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            let charts = {};

            function destroyChart(id){
              if (charts[id]) { charts[id].destroy(); delete charts[id]; }
            }

            function mapToLabelsAndValues(obj){
              const labels = Object.keys(obj || {});
              const values = labels.map(k => Number(obj[k] || 0));
              return { labels, values };
            }

            function buildPie(canvasId, dataObj){
              destroyChart(canvasId);
              const el = document.getElementById(canvasId);
              if (!el) return;

              const {labels, values} = mapToLabelsAndValues(dataObj);

              charts[canvasId] = new Chart(el.getContext('2d'), {
                type: 'pie',
                data: { labels, datasets: [{ data: values }] },
                options: {
                  responsive: true,
                  plugins: { legend: { position: 'bottom' } }
                }
              });
            }

            function buildLeadsLine(canvasId, yearA, yearB){
              destroyChart(canvasId);
              const el = document.getElementById(canvasId);
              if (!el) return;

              const a = (comparisonData.leads_monthly && comparisonData.leads_monthly[yearA]) ? comparisonData.leads_monthly[yearA] : [];
              const b = (comparisonData.leads_monthly && comparisonData.leads_monthly[yearB]) ? comparisonData.leads_monthly[yearB] : [];

              charts[canvasId] = new Chart(el.getContext('2d'), {
                type: 'line',
                data: {
                  labels: months,
                  datasets: [
                    { label: String(yearA), data: a.map(Number) },
                    { label: String(yearB), data: b.map(Number) }
                  ]
                },
                options: {
                  responsive: true,
                  plugins: { legend: { position: 'top' } },
                  scales: { y: { beginAtZero: true } }
                }
              });
            }

            function render(){
              const yearA = Number(document.getElementById('dcYearA').value);
              const yearB = Number(document.getElementById('dcYearB').value);

              buildLeadsLine('dcLeadsLine', yearA, yearB);

              buildPie('dcSalesPortalA',       (comparisonData.sales_by_portal||{})[yearA]);
              buildPie('dcSalesPortalB',       (comparisonData.sales_by_portal||{})[yearB]);

              buildPie('dcCommissionPortalA',  (comparisonData.commission_by_portal||{})[yearA]);
              buildPie('dcCommissionPortalB',  (comparisonData.commission_by_portal||{})[yearB]);

              buildPie('dcSalesSourceA',       (comparisonData.sales_by_source||{})[yearA]);
              buildPie('dcSalesSourceB',       (comparisonData.sales_by_source||{})[yearB]);

              buildPie('dcCommissionSourceA',  (comparisonData.commission_by_source||{})[yearA]);
              buildPie('dcCommissionSourceB',  (comparisonData.commission_by_source||{})[yearB]);

              buildPie('dcNationalityA',       (comparisonData.sales_by_nationality||{})[yearA]);
              buildPie('dcNationalityB',       (comparisonData.sales_by_nationality||{})[yearB]);
            }

            document.getElementById('dcApply')?.addEventListener('click', render);
            render();
          })();
        </script>
      @endif
    </div>
  </div>
</div>

<script>
(function () {
  // ======================
  // A) Dashboard <-> Comparison tab switching
  // ======================
  const tabDashboard  = document.getElementById('salesDashboardTab');
  const tabComparison = document.getElementById('salesComparisonTab');

  const btnOpen = document.getElementById('openSalesComparison');
  const btnBack = document.getElementById('backToSalesDashboard');

  function showDashboard() {
    tabDashboard?.classList.remove('hidden');
    tabComparison?.classList.add('hidden');
  }

  function showComparison() {
    tabDashboard?.classList.add('hidden');
    tabComparison?.classList.remove('hidden');
  }

  btnOpen?.addEventListener('click', showComparison);
  btnBack?.addEventListener('click', showDashboard);

  // ======================
  // B) Chart.js check
  // ======================
  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded on this partial.');
    return;
  }

  // ======================
  // 1) Monthly Leads Charts
  // ======================
  const leadStats = @json($leadStats ?? []);

  if (Array.isArray(leadStats) && leadStats.length) {
    const monthsFull = leadStats.map(i => i.month);
    const leadsFull  = leadStats.map(i => Number(i.total) || 0);

    const idxByMonth = monthsFull.reduce((acc, m, i) => (acc[m] = i, acc), {});
    const sliceByMonths = (months) => {
      const labels = months.filter(m => m in idxByMonth);
      const data   = labels.map(m => leadsFull[idxByMonth[m]]);
      return { labels, data };
    };

    const Q1M = ['January','February','March'];
    const Q2M = ['April','May','June'];
    const Q3M = ['July','August','September'];
    const Q4M = ['October','November','December'];

    const SL_ALL = { labels: monthsFull, data: leadsFull };
    const SL_Q1  = sliceByMonths(Q1M);
    const SL_Q2  = sliceByMonths(Q2M);
    const SL_Q3  = sliceByMonths(Q3M);
    const SL_Q4  = sliceByMonths(Q4M);

    let leadsBar = null;
    const leadsCompareCache = {};

    function makeBar(canvasId, labels, data) {
      const canvas = document.getElementById(canvasId);
      if (!canvas) return null;
      return new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Leads', data }] },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { grid: { display: false } }
          },
          plugins: { legend: { display: true, position: 'top' } }
        }
      });
    }

    function renderLeads(labels, data) {
      if (leadsBar) leadsBar.destroy();
      leadsBar = makeBar('leadsBar', labels, data);
    }

    function showSingle(view) {
      const grid   = document.getElementById('leadsCompareGrid');
      const canvas = document.getElementById('leadsBar');
      const title  = document.getElementById('leadsTitle');
      if (!grid || !canvas || !title) return;

      grid.style.display   = 'none';
      canvas.style.display = 'block';

      switch (view) {
        case 'q1': renderLeads(SL_Q1.labels, SL_Q1.data); title.textContent = 'Leads January – March'; break;
        case 'q2': renderLeads(SL_Q2.labels, SL_Q2.data); title.textContent = 'Leads April – June'; break;
        case 'q3': renderLeads(SL_Q3.labels, SL_Q3.data); title.textContent = 'Leads July – September'; break;
        case 'q4': renderLeads(SL_Q4.labels, SL_Q4.data); title.textContent = 'Leads October – December'; break;
        default:   renderLeads(SL_ALL.labels, SL_ALL.data); title.textContent = 'Leads January – December';
      }
    }

    function renderLeadsCompare() {
      const grid   = document.getElementById('leadsCompareGrid');
      const canvas = document.getElementById('leadsBar');
      const title  = document.getElementById('leadsTitle');
      if (!grid || !canvas || !title) return;

      canvas.style.display = 'none';
      grid.style.display   = 'grid';
      title.textContent    = 'Leads by Quarter';

      [
        ['barQ1', SL_Q1],
        ['barQ2', SL_Q2],
        ['barQ3', SL_Q3],
        ['barQ4', SL_Q4],
      ].forEach(([id, sl]) => {
        if (!leadsCompareCache[id]) {
          leadsCompareCache[id] = makeBar(id, sl.labels, sl.data);
        }
      });
    }

    renderLeads(SL_ALL.labels, SL_ALL.data);

    const leadsControls = document.getElementById('leadsControls');
    leadsControls?.addEventListener('click', (e) => {
      const btn = e.target.closest('.qbtn');
      if (!btn) return;

      leadsControls.querySelectorAll('.qbtn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const v = btn.dataset.view;
      if (v === 'compare') renderLeadsCompare();
      else showSingle(v);
    });
  }

  // ======================
  // 2) Year dropdown → reload partial
  // ======================
  const yearSelect = document.getElementById('reportYear');
  if (yearSelect) {
    yearSelect.addEventListener('change', () => {
      const y = yearSelect.value;

      const container = document.getElementById('report-content');
      if (!container) {
        console.error('Missing #report-content wrapper. Add <div id="report-content"></div> around your partial output.');
        return;
      }

      // IMPORTANT: update this endpoint to your SALES partial route if needed
      // If this blade is for sales, change "leads" below to "sales" once your route exists.
      const url = `/report/partials/sales?year=${encodeURIComponent(y)}`;

      const csvBtn = document.getElementById('downloadCsvBtn');
      const pdfBtn = document.getElementById('downloadPdfBtn');
      if (csvBtn) csvBtn.href = `/reports/export/csv/sales?year=${encodeURIComponent(y)}`;
      if (pdfBtn) pdfBtn.href = `/reports/export/pdf/sales?year=${encodeURIComponent(y)}`;

      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
          const wrap = document.createElement('div');
          wrap.innerHTML = html;
          container.replaceChildren(...wrap.childNodes);

          container.querySelectorAll('script').forEach(old => {
            const s = document.createElement('script');
            [...old.attributes].forEach(a => s.setAttribute(a.name, a.value));
            s.textContent = old.textContent;
            old.replaceWith(s);
          });
        })
        .catch(err => {
          console.error(err);
          container.innerHTML = `
            <div class="text-red-600 p-4">
              Could not load sales data for year <strong>${y}</strong>.
            </div>`;
        });
    });
  }

  // ======================
  // 3) Pie charts (Location & Source)
  // ======================
  const leadsByLocation = @json($leadsByLocation ?? []);
  const leadsBySource   = @json($leadsBySource ?? []);

  function randomPastelColor() {
    const r = 150 + Math.floor(Math.random() * 105);
    const g = 150 + Math.floor(Math.random() * 105);
    const b = 150 + Math.floor(Math.random() * 105);
    return `rgb(${r},${g},${b})`;
  }

  function makePie(canvasId, labels, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !labels.length) return;

    new Chart(canvas.getContext('2d'), {
      type: 'pie',
      data: {
        labels,
        datasets: [{ data, backgroundColor: labels.map(() => randomPastelColor()) }]
      },
      options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
    });
  }

  const locLabels = Object.keys(leadsByLocation).sort();
  const locData   = locLabels.map(k => parseInt(leadsByLocation[k] || 0, 10));

  const srcLabels = Object.keys(leadsBySource).sort();
  const srcData   = srcLabels.map(k => parseInt(leadsBySource[k] || 0, 10));

  makePie('pieLocation', locLabels, locData);
  makePie('pieSource',   srcLabels, srcData);

})();
</script>
