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
@endphp
<div id="leadsContainer">
    <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white rounded-lg shadow-md">

        {{-- Toolbar --}}
        <div class="mb-6 rounded-lg border border-gray-200 bg-white/90 p-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
            <h2 class="text-xl font-semibold text-gray-800">Leads Report</h2>
            <p class="text-sm text-gray-500">Use the filters to change what’s shown below.</p>
            </div>

            <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col">
                <button id="btnDataComparison" class="qbtn">Data Comparison</button>
                <!-- <label for="reportYear" class="text-xs uppercase tracking-wide text-gray-500 mb-1">Year</label> -->
                <select id="reportYear" class="border border-gray-300 rounded px-3 py-2 text-sm w-40 md:w-52">
                @foreach($reportYears as $y)
                    <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
                </select>
            </div>
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-center mb-8">
            <div class="bg-green-500 text-white py-4 rounded-lg shadow-md">
                <h3 class="text-lg font-bold">SLV Site</h3>
                <p class="text-2xl font-semibold">{{ number_format((int)($kpiSlv ?? 0)) }}</p>
            </div>

            <div class="bg-yellow-500 text-white py-4 rounded-lg shadow-md">
                <h3 class="text-lg font-bold">APITS</h3>
                <p class="text-2xl font-semibold">{{ number_format((int)($kpiApits ?? 0)) }}</p>
            </div>

            <div class="bg-blue-500 text-white py-4 rounded-lg shadow-md">
                <h3 class="text-lg font-bold">Zoopla</h3>
                <p class="text-2xl font-semibold">{{ number_format((int)($kpiZoopla ?? 0)) }}</p>
            </div>

            <div class="bg-pink-500 text-white py-4 rounded-lg shadow-md">
                <h3 class="text-lg font-bold">RightMove</h3>
                <p class="text-2xl font-semibold">{{ number_format((int)($kpiRightmove ?? 0)) }}</p>
            </div>
        </div>


        {{-- Total Leads --}}
        <div class="mb-6">
            <h4 class="text-md font-semibold mb-1">Total Leads</h4>
            <div class="w-full bg-gray-300 rounded-full h-4">
                <div class="bg-green-600 h-4 rounded-full" style="width: 100%"></div>
            </div>
            <div class="mt-2 text-sm text-gray-700 font-semibold">
                {{ number_format((int)($totalLeadsKpi ?? 0)) }}
            </div>
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

        {{-- NEW: Visual Breakdown (Pie charts) --}}
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
</div>

<div id="comparisonContainer" class="hidden p-6 bg-white rounded-lg shadow-md">
    @include('report.partials.data-comparison')
</div>


<script>
(function () {
  // ======================
  // A) Leads <-> Data Comparison toggle
  // ======================
  const leadsContainer      = document.getElementById('leadsContainer');
  const comparisonContainer = document.getElementById('comparisonContainer');
  const btnDataComparison   = document.getElementById('btnDataComparison');
  const backToLeads         = document.getElementById('backToLeads'); // must exist inside data-comparison partial

  if (btnDataComparison && leadsContainer && comparisonContainer) {
    btnDataComparison.addEventListener('click', () => {
      leadsContainer.classList.add('hidden');
      comparisonContainer.classList.remove('hidden');
    });
  }

  if (backToLeads && leadsContainer && comparisonContainer) {
    backToLeads.addEventListener('click', () => {
      comparisonContainer.classList.add('hidden');
      leadsContainer.classList.remove('hidden');
    });
  }

  // ======================
  // B) Chart.js check
  // ======================
  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded on the leads partial.');
    return;
  }

  // ======================
  // 1) Monthly Leads Charts
  // ======================
  const leadStats = @json($leadStats ?? []);

  console.log('leadStats from Blade:', leadStats);

  if (!Array.isArray(leadStats) || leadStats.length === 0) {
    console.warn('No leadStats data – skipping chart initialisation.');
    // still allow year dropdown / toggles to work
  } else {
    const monthsFull = leadStats.map(i => i.month);
    const leadsFull  = leadStats.map(i => Number(i.total) || 0);

    const idxByMonth = monthsFull.reduce((acc, m, i) => {
      acc[m] = i;
      return acc;
    }, {});

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
      if (!canvas) {
        console.warn('Bar canvas not found:', canvasId);
        return null;
      }
      const ctx = canvas.getContext('2d');

      return new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Leads',
            data
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { grid: { display: false } }
          },
          plugins: {
            legend: { display: true, position: 'top' }
          }
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
        case 'q1':
          renderLeads(SL_Q1.labels, SL_Q1.data);
          title.textContent = 'Leads January – March';
          break;
        case 'q2':
          renderLeads(SL_Q2.labels, SL_Q2.data);
          title.textContent = 'Leads April – June';
          break;
        case 'q3':
          renderLeads(SL_Q3.labels, SL_Q3.data);
          title.textContent = 'Leads July – September';
          break;
        case 'q4':
          renderLeads(SL_Q4.labels, SL_Q4.data);
          title.textContent = 'Leads October – December';
          break;
        default:
          renderLeads(SL_ALL.labels, SL_ALL.data);
          title.textContent = 'Leads January – December';
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

    // Initial render
    renderLeads(SL_ALL.labels, SL_ALL.data);

    // Quarter buttons
    const leadsControls = document.getElementById('leadsControls');
    if (leadsControls) {
      leadsControls.addEventListener('click', (e) => {
        const btn = e.target.closest('.qbtn');
        if (!btn) return;

        leadsControls.querySelectorAll('.qbtn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const v = btn.dataset.view;
        if (v === 'compare') renderLeadsCompare();
        else showSingle(v);
      });
    }
  }

  // ======================
  // 2) Year dropdown → reload partial
  // ======================
  const yearSelect = document.getElementById('reportYear');
  if (yearSelect) {
    yearSelect.addEventListener('change', () => {
      const y = yearSelect.value;

      // IMPORTANT: this element MUST exist on the main report page (not inside the partial)
      const container = document.getElementById('report-content');
      if (!container) {
        console.error('Missing #report-content wrapper. Add <div id="report-content"></div> around your partial output.');
        return;
      }

      const url = `/report/partials/leads?year=${encodeURIComponent(y)}`;

      // Update CSV/PDF links (these IDs must match your buttons in the page header)
      const csvBtn = document.getElementById('downloadCsvBtn');
      const pdfBtn = document.getElementById('downloadPdfBtn');
      if (csvBtn) csvBtn.href = `/reports/export/csv/leads?year=${encodeURIComponent(y)}`;
      if (pdfBtn) pdfBtn.href = `/reports/export/pdf/leads?year=${encodeURIComponent(y)}`;

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
        .catch(err => {
          console.error(err);
          container.innerHTML = `
            <div class="text-red-600 p-4">
              Could not load leads data for year <strong>${y}</strong>.
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
    if (!canvas || labels.length === 0) {
      console.warn('Pie canvas not found or empty data:', canvasId);
      return;
    }
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
      type: 'pie',
      data: {
        labels,
        datasets: [{
          data,
          backgroundColor: labels.map(() => randomPastelColor())
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } }
      }
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

