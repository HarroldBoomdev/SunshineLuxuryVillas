<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">Deals Report</h2>
            <p class="text-sm text-gray-500">Pipeline health, stage breakdown, and deal aging.</p>
        </div>

        <div class="flex gap-2 items-center">
            <button class="btn btn-outline btn-sm">Data Comparison</button>

            <select class="form-select form-select-sm" id="dealsYear">
                @foreach($reportYears as $y)
                    <option value="{{ $y }}" @selected($y === $selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card bg-blue-500 text-white">
            <p class="text-sm opacity-80">Active Deals</p>
            <p class="text-3xl font-bold">{{ $kpis['active_deals'] }}</p>
            <p class="text-xs opacity-80">in pipeline</p>
        </div>

        <div class="card bg-green-500 text-white">
            <p class="text-sm opacity-80">Closed This Month</p>
            <p class="text-3xl font-bold">{{ $kpis['closed_this_month'] }}</p>
            <p class="text-xs opacity-80">wins</p>
        </div>

        <div class="card bg-purple-500 text-white">
            <p class="text-sm opacity-80">Pipeline Value</p>
            <p class="text-2xl font-bold">€{{ number_format($kpis['pipeline_value']) }}</p>
            <p class="text-xs opacity-80">open deals</p>
        </div>

        <div class="card bg-pink-500 text-white">
            <p class="text-sm opacity-80">Avg Deal Age</p>
            <p class="text-3xl font-bold">{{ $kpis['avg_age_days'] }}</p>
            <p class="text-xs opacity-80">days</p>
        </div>
    </div>

    {{-- Progress + Goal --}}
    <div class="card">
        <div class="flex items-center justify-between mb-2">
            <h3 class="card-title">Pipeline Progress</h3>
            <span class="text-xs text-gray-400">{{ $pipelineProgress['label'] }}</span>
        </div>

        <div class="progress" style="height: 10px;">
            <div class="progress-bar" role="progressbar"
                 style="width: {{ $pipelineProgress['percent'] }}%;"
                 aria-valuenow="{{ $pipelineProgress['percent'] }}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>

        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span>{{ $pipelineProgress['percent'] }}% toward monthly goal</span>
            <span>Goal: {{ $pipelineProgress['goal'] }} closed deals</span>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <h3 class="card-title">Deals by Stage</h3>
                <span class="text-xs text-gray-400">distribution</span>
            </div>
            <canvas id="chartDealsStages" height="120"></canvas>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <h3 class="card-title">Deal Aging (Days)</h3>
                <span class="text-xs text-gray-400">oldest deals first</span>
            </div>
            <canvas id="chartDealsAging" height="120"></canvas>
        </div>
    </div>

    {{-- Deals Table --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title">Pipeline Deals</h3>
            <span class="text-xs text-gray-400">dummy now (replace with DB later)</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Deal</th>
                    <th>Client</th>
                    <th>Agent</th>
                    <th>Stage</th>
                    <th>Value</th>
                    <th>Created</th>
                    <th>Age</th>
                    <th>Next Action</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dealsTable as $d)
                    <tr>
                        <td class="font-medium">{{ $d['deal'] }}</td>
                        <td>{{ $d['client'] }}</td>
                        <td>{{ $d['agent'] }}</td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $d['stage'] }}</span>
                        </td>
                        <td>€{{ number_format($d['value']) }}</td>
                        <td>{{ $d['created_at'] }}</td>
                        <td>{{ $d['age_days'] }}d</td>
                        <td>{{ $d['next_action'] }}</td>
                        <td>
                            @php
                                $badge = match($d['status']) {
                                    'Active' => 'badge-success',
                                    'Stale' => 'badge-warning',
                                    'At Risk' => 'badge-danger',
                                    default => 'badge-light'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $d['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
(function () {
    if (typeof Chart === 'undefined') return;

    const stages = @json($chartStages ?? ['labels'=>[], 'values'=>[]]);
    const aging  = @json($chartAging ?? ['labels'=>[], 'values'=>[]]);

    const elStages = document.getElementById('chartDealsStages');
    const elAging  = document.getElementById('chartDealsAging');

    if (elStages) {
        new Chart(elStages, {
            type: 'doughnut',
            data: {
                labels: stages.labels,
                datasets: [{ data: stages.values }]
            },
            options: { responsive: true }
        });
    }

    if (elAging) {
        new Chart(elAging, {
            type: 'bar',
            data: {
                labels: aging.labels,
                datasets: [{ label: 'Days', data: aging.values }]
            },
            options: { responsive: true }
        });
    }
})();
</script>
