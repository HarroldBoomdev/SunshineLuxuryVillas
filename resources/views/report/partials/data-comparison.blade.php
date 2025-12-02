

<style>
    .dc-card { @apply bg-white rounded-lg shadow p-4 border border-gray-200; }
    .dc-label { @apply block text-xs font-semibold text-gray-600 uppercase mb-1; }
    .dc-select, .dc-checkbox {
        @apply border border-gray-300 rounded px-3 py-2 text-sm;
    }
</style>

<div class="p-4 bg-white rounded-lg border border-gray-200" id="dataComparisonRoot">

    <button id="btnBackToLeads"
            class="mb-5 text-blue-600 hover:underline flex items-center gap-2">
        ← Back to Leads
    </button>

    <h2 class="text-xl font-bold mb-6">Data Comparison</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Years selector --}}
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

        {{-- Metric selector --}}
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

        {{-- Compare button --}}
        <div class="dc-card flex items-end">
            <button id="btnRunComparison"
                    class="bg-blue-600 text-white px-4 py-2 rounded w-full hover:bg-blue-700">
                Run Comparison
            </button>
        </div>

    </div>

    {{-- ============================== --}}
    {{-- GRAPH AREA --}}
    {{-- ============================== --}}
    <div class="dc-card mb-6">
        <h3 class="text-md font-semibold mb-3">Comparison Chart</h3>
        <canvas id="dcChart" height="140"></canvas>
    </div>

    {{-- ============================== --}}
    {{-- YEAR-ON-YEAR TABLE --}}
    {{-- ============================== --}}
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

</div>

    <div id="dataComparisonSection" class="p-4 bg-white rounded-lg border border-gray-200 mt-8">
        <h2 class="text-lg font-semibold mb-4">Data Comparison</h2>

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



{{-- ============================== --}}
{{-- CHART.JS --}}
{{-- ============================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
(function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded for Data Comparison.');
        return;
    }

    // ======================================
    // 0) SERVER DATA FOR BOTTOM COMPARISON
    // ======================================
    const allLeadsByYear = @json($comparisonData['leadsByYear'] ?? []);
    const allMetrics     = @json($comparisonData['metrics'] ?? []);

    // ======================================
    // A. TOP: LEADS 2022–2024 LINE CHART
    // ======================================
    let yoyChart = null;
    let yoyCache = null;

    function randomColor() {
        const r = Math.floor(80 + Math.random() * 150);
        const g = Math.floor(80 + Math.random() * 150);
        const b = Math.floor(80 + Math.random() * 150);
        return `rgb(${r},${g},${b})`;
    }

    function buildYoyDatasets(selectedYears) {
        if (!yoyCache) return [];
        const datasets = [];

        selectedYears.forEach(y => {
            const seriesForYear = yoyCache.series[y];
            if (!seriesForYear) return;

            datasets.push({
                label: String(y),
                data: seriesForYear,
                borderColor: randomColor(),
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.25,
            });
        });

        return datasets;
    }

    function renderYoyChart(selectedYears) {
        if (!yoyCache) return;

        const canvas = document.getElementById('lineLeadsYoY');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        const datasets = buildYoyDatasets(selectedYears);

        if (yoyChart) {
            yoyChart.destroy();
        }

        yoyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: yoyCache.months || [],
                datasets: datasets,
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
        fetch('/reports/api/leads-trend')
            .then(r => r.json())
            .then(data => {
                if (!data || !Array.isArray(data.months)) {
                    console.warn('Invalid data from /reports/api/leads-trend', data);
                    return;
                }
                yoyCache = data;

                // Start with all available years
                const checkboxEls = Array.from(document.querySelectorAll('.year-toggle'));
                const years = checkboxEls.map(cb => parseInt(cb.value, 10)).filter(Boolean);

                renderYoyChart(years);

                // Bind checkbox changes
                checkboxEls.forEach(cb => {
                    cb.addEventListener('change', () => {
                        const selected = checkboxEls
                            .filter(c => c.checked)
                            .map(c => parseInt(c.value, 10))
                            .filter(Boolean);

                        if (!selected.length) {
                            // prevent empty chart, re-check current checkbox
                            cb.checked = true;
                            selected.push(parseInt(cb.value, 10));
                        }

                        renderYoyChart(selected);
                    });
                });
            })
            .catch(err => {
                console.error('Error loading leads trend:', err);
            });
    }

    // ======================================
    // B. PORTAL YEAR-ON-YEAR TABLE (MIDDLE)
    // ======================================
    function renderPortalTable(baseYear, compareYear) {
        if (!baseYear || !compareYear || baseYear === compareYear) {
            return;
        }

        const url = `/reports/api/portal-comparison?base_year=${encodeURIComponent(baseYear)}&compare_year=${encodeURIComponent(compareYear)}`;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#portalComparisonTable tbody');
                if (!tbody) return;

                tbody.innerHTML = '';

                // Update headers
                const thBaseYear   = document.getElementById('thBaseYear');
                const thBasePct    = document.getElementById('thBasePct');
                const thCompYear   = document.getElementById('thCompYear');
                const thCompPct    = document.getElementById('thCompPct');

                if (thBaseYear) thBaseYear.textContent = `${data.base_year} Total`;
                if (thBasePct)  thBasePct.textContent  = `% of ${data.base_year}`;
                if (thCompYear) thCompYear.textContent = `${data.compare_year} Total`;
                if (thCompPct)  thCompPct.textContent  = `% of ${data.compare_year}`;

                if (!Array.isArray(data.portals)) return;

                data.portals.forEach(row => {
                    const delta = row.delta_pct;
                    let deltaClass = '';
                    if (delta !== null && delta !== undefined) {
                        if (delta > 0) deltaClass = 'text-green-600';
                        if (delta < 0) deltaClass = 'text-red-600';
                    }

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="border p-2">${row.portal}</td>
                        <td class="border p-2 text-right">${row.base_total}</td>
                        <td class="border p-2 text-right">${row.base_pct_of_year ?? 0}%</td>
                        <td class="border p-2 text-right">${row.compare_total}</td>
                        <td class="border p-2 text-right">${row.compare_pct_of_year ?? 0}%</td>
                        <td class="border p-2 text-right ${deltaClass}">
                            ${delta === null ? '-' : (delta > 0 ? '+' + delta : delta) }%
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error('Error loading portal comparison:', err);
            });
    }

    function initPortalTable() {
        const baseSelect    = document.getElementById('portalBaseYear');
        const compareSelect = document.getElementById('portalCompareYear');

        if (!baseSelect || !compareSelect) return;

        function refresh() {
            const baseYear    = baseSelect.value;
            const compareYear = compareSelect.value;
            renderPortalTable(baseYear, compareYear);
        }

        baseSelect.addEventListener('change', refresh);
        compareSelect.addEventListener('change', refresh);

        // initial load
        refresh();
    }

    // ======================================
    // C. BOTTOM: CUSTOM METRIC COMPARISON
    // ======================================
    let dcChart = null;

    function dcMakeLineChart(labels, datasets) {
        const canvas = document.getElementById('dcChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        if (dcChart) dcChart.destroy();

        dcChart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    function runBottomComparison() {
        const yearCheckboxes = Array.from(document.querySelectorAll('.dc-year'));
        if (!yearCheckboxes.length) return;

        const selectedYears = yearCheckboxes
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (!selectedYears.length) {
            alert('Please select at least 1 year.');
            return;
        }

        const metricSelect = document.getElementById('dcMetric');
        const metric = metricSelect ? metricSelect.value : 'leads';

        const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const datasets = [];

        selectedYears.forEach(year => {
            const yearMetrics = allMetrics[year];
            if (!yearMetrics || !yearMetrics[metric]) return;

            datasets.push({
                label: String(year),
                data: yearMetrics[metric],
                borderColor: randomColor(),
                backgroundColor: 'transparent',
                tension: 0.25,
                borderWidth: 2
            });
        });

        if (!datasets.length) {
            console.warn('No datasets for metric:', metric, 'with years:', selectedYears);
            return;
        }

        dcMakeLineChart(labels, datasets);

        // Summary table
        const tbody = document.getElementById('dcSummaryTable');
        if (!tbody) return;
        tbody.innerHTML = '';

        selectedYears.forEach((year, index) => {
            const series = (allMetrics[year] && allMetrics[year][metric]) || [];
            const total  = series.reduce((sum, v) => sum + (Number(v) || 0), 0);

            let diffText = '-';
            let pctText  = '-';
            let diffClass = '';
            let pctClass  = '';

            if (index > 0) {
                const prevYear = selectedYears[index - 1];
                const prevSeries = (allMetrics[prevYear] && allMetrics[prevYear][metric]) || [];
                const prevTotal  = prevSeries.reduce((sum, v) => sum + (Number(v) || 0), 0);

                const diff = total - prevTotal;
                diffText   = diff;

                if (diff > 0) diffClass = 'text-green-600';
                if (diff < 0) diffClass = 'text-red-600';

                if (prevTotal > 0) {
                    const pct = (diff / prevTotal) * 100;
                    pctText   = pct.toFixed(1) + '%';
                    if (pct > 0) pctClass = 'text-green-600';
                    if (pct < 0) pctClass = 'text-red-600';
                }
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="border p-2">${year}</td>
                <td class="border p-2 text-right">${total}</td>
                <td class="border p-2 text-right ${diffClass}">${diffText}</td>
                <td class="border p-2 text-right ${pctClass}">${pctText}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function initBottomComparison() {
        const btnRun = document.getElementById('btnRunComparison');
        if (btnRun) {
            btnRun.addEventListener('click', runBottomComparison);
        }

        // auto-select last up to 3 years and run once
        const boxes = Array.from(document.querySelectorAll('.dc-year'));
        if (boxes.length) {
            boxes.sort((a, b) => parseInt(a.value, 10) - parseInt(b.value, 10));
            const lastThree = boxes.slice(-3);
            lastThree.forEach(cb => cb.checked = true);

            if (Object.keys(allMetrics || {}).length) {
                runBottomComparison();
            }
        }
    }

    // ======================================
    // D. TOGGLE LEADS ↔ DATA COMPARISON
    // ======================================
    const leadsContainer      = document.getElementById('leadsContainer');
    const comparisonContainer = document.getElementById('comparisonContainer');
    const btnDataComparison   = document.getElementById('btnDataComparison');
    const btnBackToLeads      = document.getElementById('btnBackToLeads') || document.getElementById('backToLeads');

    if (btnDataComparison && leadsContainer && comparisonContainer) {
        btnDataComparison.addEventListener('click', function (e) {
            e.preventDefault();
            leadsContainer.classList.add('hidden');
            comparisonContainer.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    if (btnBackToLeads && leadsContainer && comparisonContainer) {
        btnBackToLeads.addEventListener('click', function (e) {
            e.preventDefault();
            comparisonContainer.classList.add('hidden');
            leadsContainer.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ======================================
    // INIT ALL PARTS
    // ======================================
    initYoyChart();
    initPortalTable();
    initBottomComparison();
})();
</script>
