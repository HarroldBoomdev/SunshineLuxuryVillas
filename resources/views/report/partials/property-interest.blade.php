<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">Property Interest</h2>
            <p class="text-sm text-gray-500">
                Buyer engagement, views, saves, and inquiry activity
            </p>
        </div>

        <div class="flex gap-2">
            <select class="form-select">
                @foreach($reportYears as $y)
                    <option value="{{ $y }}" @selected($y === $selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

    <div class="card bg-blue-500 text-white">
        <p class="text-sm opacity-80">Most Viewed Property</p>
        <p class="text-xl font-bold">{{ $kpis['most_viewed'] }}</p>
    </div>

    <div class="card bg-indigo-500 text-white">
        <p class="text-sm opacity-80">Most Saved Property</p>
        <p class="text-xl font-bold">{{ $kpis['most_saved'] }}</p>
    </div>

    <div class="card bg-green-500 text-white">
        <p class="text-sm opacity-80">Inquiries (This Week)</p>
        <p class="text-xl font-bold">{{ $kpis['inquiries'] }}</p>
    </div>

    <div class="card bg-purple-500 text-white">
        <p class="text-sm opacity-80">Viewings Scheduled</p>
        <p class="text-xl font-bold">{{ $kpis['viewings'] }}</p>
    </div>

</div>
b

    {{-- Interest Funnel --}}
    <div class="card">
        <h3 class="card-title">Buyer Interest Funnel</h3>
        <canvas id="interestFunnelChart" height="120"></canvas>
    </div>

    {{-- Interest by Category --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="card">
            <h3 class="card-title">Interest by Region</h3>
            <canvas id="interestByRegion"></canvas>
        </div>

        <div class="card">
            <h3 class="card-title">Interest by Property Type</h3>
            <canvas id="interestByType"></canvas>
        </div>

        <div class="card">
            <h3 class="card-title">Interest by Price Range</h3>
            <canvas id="interestByPrice"></canvas>
        </div>
    </div>

    {{-- Hot Properties Table --}}
    <div class="card">
        <h3 class="card-title mb-4">Hot Properties (Highest Interest)</h3>

        <table class="table">
            <thead>
            <tr>
                <th>Property</th>
                <th>Type</th>
                <th>Region</th>
                <th>Views</th>
                <th>Saves</th>
                <th>Inquiries</th>
                <th>Viewings</th>
                <th>Score</th>
            </tr>
            </thead>
            <tbody>
            @foreach($hotProperties as $row)
                <tr>
                    <td class="font-medium">{{ $row['name'] }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['region'] }}</td>
                    <td>{{ $row['views'] }}</td>
                    <td>{{ $row['saves'] }}</td>
                    <td>{{ $row['inquiries'] }}</td>
                    <td>{{ $row['viewings'] }}</td>
                    <td>
                        <span class="badge badge-danger">{{ $row['score'] }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Charts --}}
<script>
(function () {

    const funnel = @json($chartFunnel);
    const byRegion = @json($chartByRegion);
    const byType = @json($chartByType);
    const byPrice = @json($chartByPrice);

    if (typeof Chart === 'undefined') return;

    new Chart(document.getElementById('interestFunnelChart'), {
        type: 'bar',
        data: {
            labels: funnel.labels,
            datasets: [{
                data: funnel.values,
                backgroundColor: '#6366f1'
            }]
        }
    });

    new Chart(document.getElementById('interestByRegion'), {
        type: 'bar',
        data: {
            labels: byRegion.labels,
            datasets: [{ data: byRegion.values }]
        }
    });

    new Chart(document.getElementById('interestByType'), {
        type: 'bar',
        data: {
            labels: byType.labels,
            datasets: [{ data: byType.values }]
        }
    });

    new Chart(document.getElementById('interestByPrice'), {
        type: 'bar',
        data: {
            labels: byPrice.labels,
            datasets: [{ data: byPrice.values }]
        }
    });

})();
</script>
