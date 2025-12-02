{{-- resources/views/reports/2023.blade.php --}}
<div class="mb-6 rounded-lg border border-gray-200 bg-white/90 p-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
  <div>
    <h2 class="text-xl font-semibold text-gray-800">Leads & Sales Report</h2>
    <p class="text-sm text-gray-500">Use the dropdown to switch year.</p>
  </div>

  <div class="flex flex-wrap items-end gap-4">
    <div class="flex flex-col">
      <label for="reportYear" class="text-xs uppercase tracking-wide text-gray-500 mb-1">Year</label>
      <select id="reportYear" class="border border-gray-300 rounded px-3 py-2 text-sm w-40 md:w-52">
        @foreach($reportYears as $y)
          <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>
            {{ $y }}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>



@php
    /*
     |--------------------------------------------------------------
     |  STATIC DATA FOR 2023 – replace with real values later
     |--------------------------------------------------------------
    */

    // Monthly labels
    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // Example: monthly sales count (number of completed sales)
    $monthlySales = [2, 3, 1, 4, 3, 2, 3, 4, 2, 3, 1, 3];

    // Example: monthly commission (EUR)
    $monthlyCommission = [18000, 22000, 9000, 26000, 21000, 15000, 23000, 25000, 19000, 21000, 8000, 24000];

    // Totals / KPIs (put your real 2023 numbers here)
    $totalLeads       = 2321;
    $totalViewings    = 518;
    $totalSales       = 37;
    $totalCommission  = 193456; // EUR
    $averageSalePrice = 371159; // EUR
    $averageCommissionPerSale = round($totalCommission / max($totalSales,1));

    // Conversion rates
    $leadToViewing = 22;   // %
    $viewingToSale = 7;    // %
    $leadToSale    = 1.6;  // %

    // Agent performance (dummy names, replace with real)
    $agents = ['Agent 1', 'Agent 2', 'Agent 3', 'Agent 4'];
    $salesPerAgent = [10, 12, 8, 7];          // number of sales per agent
    $commissionPerAgent = [60000, 70000, 35000, 28000]; // EUR

    // Leads → Viewings → Sales funnel (for the bar chart)
    $funnelLabels = ['Leads', 'Viewings', 'Sales'];
    $funnelValues = [$totalLeads, $totalViewings, $totalSales];

    // Source of sales (percentages or absolute numbers)
    $sourceLabels = ['Portals', 'Referrals', 'Repeat Clients', 'Walk-ins', 'Social Media'];
    $sourceValues = [45, 25, 15, 5, 10]; // %

    // Demographic breakdown (buyer countries)
    $countryLabels = ['UK', 'Russia', 'Germany', 'France', 'Israel', 'Other'];
    $countryValues = [40, 20, 10, 8, 7, 15]; // %
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2023 Performance Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        body {
            background-color: #f5f5f7;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .dashboard-container {
            max-width: 1300px;
        }
        .kpi-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            background: #ffffff;
        }
        .kpi-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .25rem;
        }
        .kpi-value {
            font-size: 1.6rem;
            font-weight: 700;
        }
        .kpi-sub {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .chart-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            background: #ffffff;
        }
        .chart-card .card-header {
            background: transparent;
            border-bottom: 0;
            padding-bottom: 0;
        }
        .chart-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: .25rem;
        }
        .chart-card .card-subtitle {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .chart-wrapper {
            min-height: 260px;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4 dashboard-container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">2023 Performance Dashboard</h1>
            <p class="text-muted mb-0">Static data version – later connect to database.</p>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
            2023 Summary
        </span>
    </div>

    {{-- KPI ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card kpi-card p-3">
                <div class="kpi-label">Total Leads</div>
                <div class="kpi-value">{{ number_format($totalLeads) }}</div>
                <div class="kpi-sub">All enquiry sources combined</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card p-3">
                <div class="kpi-label">Total Viewings</div>
                <div class="kpi-value">{{ number_format($totalViewings) }}</div>
                <div class="kpi-sub">{{ $leadToViewing }}% of leads turned into viewings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card p-3">
                <div class="kpi-label">Total Sales</div>
                <div class="kpi-value">{{ number_format($totalSales) }}</div>
                <div class="kpi-sub">{{ $viewingToSale }}% of viewings turned into sales</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card p-3">
                <div class="kpi-label">Total Commission</div>
                <div class="kpi-value">€{{ number_format($totalCommission) }}</div>
                <div class="kpi-sub">Avg €{{ number_format($averageCommissionPerSale) }} per sale</div>
            </div>
        </div>
    </div>

    {{-- SECOND KPI ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card kpi-card p-3">
                <div class="kpi-label">Avg Sale Price</div>
                <div class="kpi-value">€{{ number_format($averageSalePrice) }}</div>
                <div class="kpi-sub">Across all completed sales in 2023</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card p-3">
                <div class="kpi-label">Lead → Sale Conversion</div>
                <div class="kpi-value">{{ $leadToSale }}%</div>
                <div class="kpi-sub">{{ $totalSales }} sales from {{ $totalLeads }} leads</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card p-3">
                <div class="kpi-label">Viewings per Sale</div>
                <div class="kpi-value">
                    {{ $totalSales ? number_format($totalViewings / $totalSales, 1) : '0.0' }}
                </div>
                <div class="kpi-sub">Average number of viewings needed to close</div>
            </div>
        </div>
    </div>

    {{-- TRENDS ROW --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-title">Monthly Sales</div>
                    <div class="card-subtitle">Number of completed sales per month</div>
                </div>
                <div class="card-body">
                    <div id="chart-monthly-sales" class="chart-wrapper"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-title">Monthly Commission</div>
                    <div class="card-subtitle">Total commission earned per month (€)</div>
                </div>
                <div class="card-body">
                    <div id="chart-monthly-commission" class="chart-wrapper"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- AGENT PERFORMANCE --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-title">Sales by Agent</div>
                    <div class="card-subtitle">Total number of sales per agent in 2023</div>
                </div>
                <div class="card-body">
                    <div id="chart-sales-by-agent" class="chart-wrapper"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-title">Commission by Agent</div>
                    <div class="card-subtitle">Total commission generated per agent (€)</div>
                </div>
                <div class="card-body">
                    <div id="chart-commission-by-agent" class="chart-wrapper"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- FUNNEL + SOURCES --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-title">Leads → Viewings → Sales</div>
                    <div class="card-subtitle">Funnel overview for 2023</div>
                </div>
                <div class="card-body">
                    <div id="chart-funnel" class="chart-wrapper"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-title">Sources of Sales</div>
                    <div class="card-subtitle">Where completed sales came from</div>
                </div>
                <div class="card-body">
                    <div id="chart-sources" class="chart-wrapper"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- DEMOGRAPHICS --}}
    <div class="row g-4 mb-5">
        <div class="col-xl-6">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-title">Buyer Demographics</div>
                    <div class="card-subtitle">Distribution of buyers by country</div>
                </div>
                <div class="card-body">
                    <div id="chart-demographics" class="chart-wrapper"></div>
                </div>
            </div>
        </div>

        {{-- You can add another chart/table here if needed --}}
        <div class="col-xl-6">
            <div class="card chart-card h-100">
                <div class="card-header">
                    <div class="card-title">2023 Summary</div>
                    <div class="card-subtitle">Quick text summary matching your Google doc</div>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>{{ $totalLeads }} leads generated across all sources.</li>
                        <li>{{ $totalViewings }} viewings booked ({{ $leadToViewing }}% of leads).</li>
                        <li>{{ $totalSales }} sales completed ({{ $viewingToSale }}% of viewings).</li>
                        <li>Total commission: €{{ number_format($totalCommission) }}.</li>
                        <li>Average sale price: €{{ number_format($averageSalePrice) }}.</li>
                        <li>Lead → Sale conversion rate: {{ $leadToSale }}%.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // PHP → JS data
    const months              = @json($months);
    const monthlySales        = @json($monthlySales);
    const monthlyCommission   = @json($monthlyCommission);
    const agents              = @json($agents);
    const salesPerAgent       = @json($salesPerAgent);
    const commissionPerAgent  = @json($commissionPerAgent);
    const funnelLabels        = @json($funnelLabels);
    const funnelValues        = @json($funnelValues);
    const sourceLabels        = @json($sourceLabels);
    const sourceValues        = @json($sourceValues);
    const countryLabels       = @json($countryLabels);
    const countryValues       = @json($countryValues);

    // 1. Monthly Sales (line)
    new ApexCharts(document.querySelector("#chart-monthly-sales"), {
        chart: {
            type: 'line',
            height: 260,
            toolbar: { show: false }
        },
        series: [{
            name: 'Sales',
            data: monthlySales
        }],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { categories: months },
        yaxis: { title: { text: 'Number of sales' } },
        dataLabels: { enabled: false }
    }).render();

    // 2. Monthly Commission (line)
    new ApexCharts(document.querySelector("#chart-monthly-commission"), {
        chart: {
            type: 'line',
            height: 260,
            toolbar: { show: false }
        },
        series: [{
            name: 'Commission (€)',
            data: monthlyCommission
        }],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { categories: months },
        yaxis: { title: { text: 'Commission (€)' } },
        dataLabels: { enabled: false }
    }).render();

    // 3. Sales by Agent (bar)
    new ApexCharts(document.querySelector("#chart-sales-by-agent"), {
        chart: {
            type: 'bar',
            height: 260,
            toolbar: { show: false }
        },
        series: [{
            name: 'Sales',
            data: salesPerAgent
        }],
        xaxis: { categories: agents },
        dataLabels: { enabled: false },
        plotOptions: {
            bar: { columnWidth: '45%', borderRadius: 6 }
        }
    }).render();

    // 4. Commission by Agent (bar)
    new ApexCharts(document.querySelector("#chart-commission-by-agent"), {
        chart: {
            type: 'bar',
            height: 260,
            toolbar: { show: false }
        },
        series: [{
            name: 'Commission (€)',
            data: commissionPerAgent
        }],
        xaxis: { categories: agents },
        yaxis: { title: { text: '€' } },
        dataLabels: { enabled: false },
        plotOptions: {
            bar: { columnWidth: '45%', borderRadius: 6 }
        }
    }).render();

    // 5. Funnel (Leads → Viewings → Sales)
    new ApexCharts(document.querySelector("#chart-funnel"), {
        chart: {
            type: 'bar',
            height: 260,
            toolbar: { show: false }
        },
        series: [{
            name: 'Count',
            data: funnelValues
        }],
        xaxis: { categories: funnelLabels },
        plotOptions: {
            bar: {
                columnWidth: '40%',
                borderRadius: 6
            }
        },
        dataLabels: { enabled: true }
    }).render();

    // 6. Sources of Sales (donut)
    new ApexCharts(document.querySelector("#chart-sources"), {
        chart: {
            type: 'donut',
            height: 260
        },
        series: sourceValues,
        labels: sourceLabels,
        legend: { position: 'bottom' }
    }).render();

    // 7. Buyer Demographics (donut)
    new ApexCharts(document.querySelector("#chart-demographics"), {
        chart: {
            type: 'donut',
            height: 260
        },
        series: countryValues,
        labels: countryLabels,
        legend: { position: 'bottom' }
    }).render();
});
</script>
</body>
</html>
