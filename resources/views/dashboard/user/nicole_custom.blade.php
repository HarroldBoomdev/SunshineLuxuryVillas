

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Welcome, Nicole</h2>
            <p class="mb-6">This dashboard shows your current sales and leads. Sensitive reports and income data are excluded.</p>

            <!-- KPIs -->
            <div class="grid grid-cols-2 gap-4 text-center mb-8">
                <div class="bg-blue-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Total Leads (2022–2024)</h3>
                    <p class="text-2xl font-semibold">{{ $leadStats->sum('total') }}</p>
                </div>
                <div class="bg-green-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Total Sales (This Year)</h3>
                    <p class="text-2xl font-semibold">{{ $salesStats->sum('total') }}</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Monthly Leads</h3>
                    <canvas id="leadChartNicole"></canvas>
                </div>
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Monthly Sales</h3>
                    <canvas id="salesChartNicole"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const leadStats = @json($leadStats);
    const salesStats = @json($salesStats);

    const months = [...new Set([...leadStats.map(i => i.month), ...salesStats.map(i => i.month)])];

    const leadCounts = months.map(m => {
        const match = leadStats.find(i => i.month === m);
        return match ? match.total : 0;
    });

    const salesCounts = months.map(m => {
        const match = salesStats.find(i => i.month === m);
        return match ? match.total : 0;
    });

    new Chart(document.getElementById('leadChartNicole'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Leads',
                data: leadCounts,
                backgroundColor: '#3b82f6'
            }]
        }
    });

    new Chart(document.getElementById('salesChartNicole'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Sales',
                data: salesCounts,
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderColor: '#10b981',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        }
    });
</script>
