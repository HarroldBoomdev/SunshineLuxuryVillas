

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white rounded-lg shadow-md">

            <!-- Top Stats -->
            <div class="grid grid-cols-4 gap-4 text-center mb-8">
                <div class="bg-red-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Total Properties</h3>
                   <p class="text-2xl font-semibold">{{ $totalProperties }}</p>
                </div>
                <div class="bg-yellow-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Active Listings</h3>
                    <p class="text-2xl font-semibold">{{ $activeListings }}</p>
                </div>
                <div class="bg-green-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">Clients</h3>
                    <p class="text-2xl font-semibold">{{ $clients }}</p>
                </div>
                <div class="bg-pink-500 text-white py-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold">New Clients</h3>
                    <p class="text-2xl font-semibold">{{ $newClients }}</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Sales Overview</h3>
                    <canvas id="salesChart"></canvas>  0. 
                </div>
                <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Listings by Location</h3>
                    <canvas id="listingSalesChart"></canvas>
                </div>
            </div>

            <!-- Lead Summary Table -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Lead Summary (2022–2024)</h3>
                <div class="overflow-x-auto">

                    {{-- Filter Buttons --}}
                    <div class="flex justify-between items-center mb-4">
                        <div class="space-x-2">
                            <a href="{{ route('dashboard.sales.listings', ['filter' => 'today']) }}" class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded">Today</a>
                            <a href="{{ route('dashboard.sales.listings', ['filter' => 'week']) }}" class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded">This Week</a>
                            <a href="{{ route('dashboard.sales.listings', ['filter' => 'month']) }}" class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded">This Month</a>
                            <a href="{{ route('dashboard.sales.listings') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-1 px-3 rounded">All</a>
                        </div>

                        {{-- Export Buttons --}}
                        <div>
                            <a href="{{ route('export.listings.csv', ['type' => 'listings']) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded mr-2">
                                Download CSV
                            </a>
                            <a href="{{ route('export.listings.pdf', ['type' => 'listings']) }}" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded">
                                Download PDF
                            </a>

                        </div>
                    </div>

                    <table class="min-w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100 text-gray-700 font-semibold">
                            <tr>
                                <th class="px-4 py-2 border">Year</th>
                                <th class="px-4 py-2 border">Month</th>
                                <th class="px-4 py-2 border">Location</th>
                                <th class="px-4 py-2 border">Source</th>
                                <th class="px-4 py-2 border">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                                <tr class="text-center">
                                    <td class="px-4 py-2 border">{{ $lead->year }}</td>
                                    <td class="px-4 py-2 border">{{ $lead->month }}</td>
                                    <td class="px-4 py-2 border">{{ $lead->location ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 border">{{ $lead->source ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 border font-semibold">{{ $lead->count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-4">No lead data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
     const monthlyLabels = {!! json_encode($salesStats->pluck('month')) !!};
    const monthlyData = {!! json_encode($salesStats->pluck('total')) !!};

    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Sales',
                data: monthlyData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        }
    });
</script>

