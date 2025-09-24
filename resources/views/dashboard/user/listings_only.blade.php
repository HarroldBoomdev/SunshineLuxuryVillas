

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white rounded-lg shadow-md">

            <!-- KPI Section -->
            <div class="grid grid-cols-3 gap-4 text-center mb-8">
                <div class="bg-red-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Total Properties</h3>
                    <p class="text-2xl font-semibold">{{ $totalProperties }}</p>
                </div>
                <div class="bg-yellow-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Listed Today</h3>
                    <p class="text-2xl font-semibold">{{ $listedToday }}</p>
                </div>
                <div class="bg-pink-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Deleted Today</h3>
                    <p class="text-2xl font-semibold">{{ $deletedToday }}</p>
                </div>
            </div>

            <!-- Charts Section -->

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold">Listings Summary (Filtered)</h3>
                <div class="space-x-2">
                    <a href="{{ route('dashboard.listings.only', ['filter' => 'today']) }}"
                    class="py-1 px-3 rounded {{ request('filter') === 'today' ? 'bg-blue-700 text-white' : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                        Today
                    </a>
                    <a href="{{ route('dashboard.listings.only', ['filter' => 'week']) }}"
                    class="py-1 px-3 rounded {{ request('filter') === 'week' ? 'bg-blue-700 text-white' : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                        This Week
                    </a>
                    <a href="{{ route('dashboard.listings.only', ['filter' => 'month']) }}"
                    class="py-1 px-3 rounded {{ request('filter') === 'month' ? 'bg-blue-700 text-white' : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                        This Month
                    </a>
                    <a href="{{ route('dashboard.listings.only') }}"
                    class="py-1 px-3 rounded {{ request()->has('filter') ? 'bg-gray-500 text-white hover:bg-gray-600' : 'bg-gray-700 text-white' }}">
                        All
                    </a>
                </div>
                <div class="flex justify-end mb-4">
                    <a href="{{ route('export.listings.csv', ['type' => 'listings']) }}"
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded mr-2">
                        Download CSV
                    </a>
                    <a href="{{ route('export.listings.pdf', ['type' => 'listings']) }}"
                    class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded">
                        Download PDF
                    </a>
                </div>

            </div>

            <div class="grid grid-cols-3 gap-4 mb-8">
                <!-- By Type -->
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Properties by Type</h3>
                    <canvas id="typeChart"></canvas>
                </div>

                <!-- By Region -->
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Properties by Region</h3>
                    <canvas id="regionChart"></canvas>
                </div>

                <!-- By Status -->
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Properties by Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Breakdown -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Backend / Portal Feed Status</h3>
                <ul class="list-disc ml-5 text-gray-700">
                    <li>Listed on Backend: {{ $allProperties->count() }}</li>
                    <li>Live on Website: {{ $allProperties->where('is_live', true)->count() }}</li>
                    <li>On XML Feed: {{ $allProperties->where('on_xml_feed', true)->count() }}</li>
                    <li>On API Feed: {{ $allProperties->where('on_api_feed', true)->count() }}</li>
                </ul>
            </div>

        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const typeData = @json($propertyTypes);
    const regionData = @json($propertyRegions);
    const statusData = @json($propertyStatuses);

    function renderChart(id, label, dataObj, chartType = 'pie') {
        const labels = Object.keys(dataObj);
        const data = Object.values(dataObj);

        new Chart(document.getElementById(id), {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: [
                        '#f87171', '#60a5fa', '#34d399', '#fbbf24',
                        '#a78bfa', '#f472b6', '#10b981', '#6366f1'
                    ]
                }]
            }
        });
    }

    renderChart('typeChart', 'Property Types', typeData);
    renderChart('regionChart', 'Regions', regionData);
    renderChart('statusChart', 'Status', statusData);
</script>
