
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

            <!-- Charts -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Leads by Month</h3>
                    <canvas id="leadChart"></canvas>
                </div>
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Sales by Month</h3>
                    <canvas id="salesChart"></canvas>
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

    const months = [...new Set(leadStats.map(i => i.month))];
    const leadCounts = months.map(m => {
        const entry = leadStats.find(i => i.month === m);
        return entry ? entry.total : 0;
    });

    const salesCounts = months.map(m => {
        const entry = salesStats.find(i => i.month === m);
        return entry ? entry.total : 0;
    });

    // Lead Chart
    new Chart(document.getElementById('leadChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Leads',
                data: leadCounts,
                backgroundColor: '#34d399'
            }]
        }
    });

    // Sales Chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Sales',
                data: salesCounts,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        }
    });
</script>

