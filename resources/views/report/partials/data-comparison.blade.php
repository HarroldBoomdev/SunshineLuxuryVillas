

<style>
  /* Tailwind @apply DOES NOT run in-browser, so we use real CSS */
  .dc-card{
    background:#fff;border:1px solid #e5e7eb;border-radius:12px;
    padding:16px;box-shadow:0 1px 2px rgba(0,0,0,.04);
  }
  .dc-label{
    display:block;font-size:11px;font-weight:700;letter-spacing:.04em;
    color:#4b5563;text-transform:uppercase;margin-bottom:6px;
  }
  .dc-select,.dc-checkbox{border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;}
</style>

<!-- IMPORTANT: this id is what your toggle JS expects -->
<!-- <div id="comparisonContainer" class="p-4 bg-white rounded-lg border border-gray-200">



  <h2 class="text-xl font-bold mb-6">Data Comparison</h2>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="dc-card">
      <label class="dc-label">Years to Compare</label>
      @foreach($reportYears as $yr)
        <label class="flex items-center gap-2 mb-1">
          <input type="checkbox" class="dc-checkbox dc-year" value="{{ $yr }}">
          <span class="text-sm text-gray-700">{{ $yr }}</span>
        </label>
      @endforeach
      <p class="text-xs text-gray-400 mt-1">You may choose 1–3 years.</p>
    </div>

    <div class="dc-card">
      <label class="dc-label">Metric</label>
      <select id="dcMetric" class="dc-select w-full">
        <option value="leads">Total Leads</option>
        <option value="paphos">Paphos</option>
        <option value="limassol">Limassol</option>
        <option value="larnaca">Larnaca</option>
        <option value="famagusta">Famagusta</option>
        <option value="zoopla">Zoopla</option>
        <option value="rightmove">Rightmove</option>
        <option value="apits">APITS</option>
        <option value="slv">SLV</option>
        <option value="hos">HoS</option>
        <option value="facebook">Facebook</option>
      </select>
    </div>

    <div class="dc-card flex items-end">
      <button id="btnRunComparison" class="bg-blue-600 text-white px-4 py-2 rounded w-full hover:bg-blue-700">
        Run Comparison
      </button>
    </div>
  </div>

  <div class="dc-card mb-6">
    <h3 class="text-md font-semibold mb-3">Comparison Chart</h3>
    <canvas id="dcChart" height="140"></canvas>
  </div>

  <div class="dc-card">
    <h3 class="text-md font-semibold mb-3">Year-on-Year Summary</h3>
    <table class="w-full text-sm border-collapse">
      <thead>
        <tr class="bg-gray-50">
          <th class="border p-2 text-left">Year</th>
          <th class="border p-2 text-right">Total</th>
          <th class="border p-2 text-right">Diff vs Previous</th>
          <th class="border p-2 text-right">% Change</th>
        </tr>
      </thead>
      <tbody id="dcSummaryTable"></tbody>
    </table>
  </div>

</div> -->


    <div id="dataComparisonSection" class="p-4 bg-white rounded-lg border border-gray-200 mt-8">
        <!-- <h2 class="text-lg font-semibold mb-4">Data Comparison</h2> -->

         <button id="btnBackToLeads" class="mb-5 text-blue-600 hover:underline flex items-center gap-2">
            ← Back to Leads
        </button>

        {{-- TOP: multi-year leads line chart (2022–2024) --}}
        <div class="bg-gray-100 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h3 class="text-sm font-semibold text-gray-800">Leads 2022–2024</h3>
            <div class="flex flex-wrap items-center gap-3 text-xs">
                @foreach($reportYears as $y)
                <label class="inline-flex items-center gap-1">
                    <input type="checkbox" class="year-toggle" value="{{ $y }}" checked>
                    <span>{{ $y }}</span>
                </label>
                @endforeach
            </div>
            </div>
            <canvas id="lineLeadsYoY" class="chart-fixed"></canvas>
        </div>

        {{-- BOTTOM: year-on-year portals comparison table --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
            <h3 class="text-sm font-semibold text-gray-800">Year-on-Year Leads by Portal</h3>
            <div class="flex items-center gap-2 text-xs">
                <span>Base year</span>
                <select id="portalBaseYear" class="border border-gray-300 rounded px-2 py-1">
                @foreach($reportYears as $y)
                    <option value="{{ $y }}" {{ $y == 2023 ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
                </select>
                <span>vs</span>
                <select id="portalCompareYear" class="border border-gray-300 rounded px-2 py-1">
                @foreach($reportYears as $y)
                    <option value="{{ $y }}" {{ $y == 2024 ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
                </select>
            </div>
            </div>

            <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse" id="portalComparisonTable">
                <thead>
                <tr class="bg-gray-50">
                    <th class="border p-2 text-left">Portal</th>
                    <th class="border p-2 text-right"><span id="thBaseYear">Base Total</span></th>
                    <th class="border p-2 text-right"><span id="thBasePct">% of Year</span></th>
                    <th class="border p-2 text-right"><span id="thCompYear">Compare Total</span></th>
                    <th class="border p-2 text-right"><span id="thCompPct">% of Year</span></th>
                    <th class="border p-2 text-right">Δ %</th>
                </tr>
                </thead>
                <tbody>
                {{-- filled by JS from /reports/api/portal-comparison --}}
                </tbody>
            </table>
            </div>
        </div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function () {
  if (typeof Chart === 'undefined') {
    console.error('Chart.js not loaded.');
    return;
  }

  // ======================================
  // SERVER DATA FOR OTHER SECTIONS (UNCHANGED)
  // ======================================
  const allLeadsByYear = @json($comparisonData['leadsByYear'] ?? []);
  const allMetrics     = @json($comparisonData['metrics'] ?? []);

  // ✅ FIXED / STEADY COLORS PER YEAR
  const YEAR_COLORS = {
    2022: { line: '#2563eb', fill: 'rgba(37, 99, 235, 0.12)' }, // blue
    2023: { line: '#16a34a', fill: 'rgba(22, 163, 74, 0.12)' }, // green
    2024: { line: '#f59e0b', fill: 'rgba(245, 158, 11, 0.12)' }, // amber
    2025: { line: '#ef4444', fill: 'rgba(239, 68, 68, 0.12)' }, // red
  };

  function colorForYear(year) {
    // fallback if a new year appears
    const fallback = { line: '#6b7280', fill: 'rgba(107, 114, 128, 0.12)' }; // gray
    return YEAR_COLORS[year] || fallback;
  }

  // ======================================
  // A. TOP: LEADS LINE GRAPH (HARDCODED)
  // ======================================
  let yoyChart = null;

  const yoyCache = {
    months: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    series: {
      2022: [214,181,207,158,247,187,222,210,233,219,175,93],
      2023: [219,202,251,175,248,176,165,142,160,171,143,100],
      2024: [168,160,150,139,137,126,148,154,140,158,112,58],
      2025: [106,191,178,118,79,124,185,118,113,106,102,41]
    }
  };

  function buildYoyDatasets(selectedYears) {
    return selectedYears.map(year => {
      const c = colorForYear(year);
      return {
        label: String(year),
        data: yoyCache.series[year] || [],
        borderColor: c.line,
        backgroundColor: c.fill,   // you can keep 'transparent' if you don't want fill
        fill: false,               // set true if you want area fill
        borderWidth: 2,
        tension: 0.25,
        pointRadius: 2,
        pointHoverRadius: 4
      };
    });
  }

  function renderYoyChart(selectedYears) {
    const canvas = document.getElementById('lineLeadsYoY');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (yoyChart) yoyChart.destroy();

    yoyChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: yoyCache.months,
        datasets: buildYoyDatasets(selectedYears)
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' }
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });
  }

  function initYoyChart() {
    const checkboxes = Array.from(document.querySelectorAll('.year-toggle'));
    const selectedYears = checkboxes.map(cb => parseInt(cb.value, 10));

    renderYoyChart(selectedYears);

    checkboxes.forEach(cb => {
      cb.addEventListener('change', () => {
        const activeYears = checkboxes
          .filter(c => c.checked)
          .map(c => parseInt(c.value, 10));

        if (!activeYears.length) {
          cb.checked = true;
          return;
        }

        renderYoyChart(activeYears);
      });
    });
  }

  // ======================================
  // B. PORTAL YEAR-ON-YEAR TABLE (UNCHANGED)
  // ======================================
  function renderPortalTable(baseYear, compareYear) {
    if (!baseYear || !compareYear || baseYear === compareYear) return;

    fetch(`/reports/api/portal-comparison?base_year=${baseYear}&compare_year=${compareYear}`)
      .then(r => r.json())
      .then(data => {
        const tbody = document.querySelector('#portalComparisonTable tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        document.getElementById('thBaseYear').textContent = `${data.base_year} Total`;
        document.getElementById('thBasePct').textContent  = `% of ${data.base_year}`;
        document.getElementById('thCompYear').textContent = `${data.compare_year} Total`;
        document.getElementById('thCompPct').textContent  = `% of ${data.compare_year}`;

        data.portals.forEach(row => {
          const delta = row.delta_pct;
          const deltaClass = delta > 0 ? 'text-green-600' : delta < 0 ? 'text-red-600' : '';

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="border p-2">${row.portal}</td>
            <td class="border p-2 text-right">${row.base_total}</td>
            <td class="border p-2 text-right">${row.base_pct_of_year}%</td>
            <td class="border p-2 text-right">${row.compare_total}</td>
            <td class="border p-2 text-right">${row.compare_pct_of_year}%</td>
            <td class="border p-2 text-right ${deltaClass}">
              ${delta === null ? '-' : (delta > 0 ? '+' + delta : delta)}%
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
  }

  function initPortalTable() {
    const base = document.getElementById('portalBaseYear');
    const comp = document.getElementById('portalCompareYear');
    if (!base || !comp) return;

    function refresh() { renderPortalTable(base.value, comp.value); }

    base.addEventListener('change', refresh);
    comp.addEventListener('change', refresh);
    refresh();
  }

  // INIT
  initYoyChart();
  initPortalTable();

})();
</script>

