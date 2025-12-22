<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">Agents Report</h2>
            <p class="text-sm text-gray-500">Agent performance overview (leads, deals, conversion, revenue).</p>
        </div>

        <div class="flex gap-2 items-center">
            <button class="btn btn-outline btn-sm">Data Comparison</button>

            <select class="form-select form-select-sm" id="agentsYear">
                @foreach($reportYears as $y)
                    <option value="{{ $y }}" @selected($y === $selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card bg-green-500 text-white">
            <p class="text-sm opacity-80">Active Agents</p>
            <p class="text-3xl font-bold">{{ $kpis['active_agents'] }}</p>
            <p class="text-xs opacity-80">agents with activity</p>
        </div>

        <div class="card bg-blue-500 text-white">
            <p class="text-sm opacity-80">Top Closer</p>
            <p class="text-lg font-bold">{{ $kpis['top_closer'] }}</p>
            <p class="text-xs opacity-80">{{ $kpis['top_closer_deals'] }} deals closed</p>
        </div>

        <div class="card bg-purple-500 text-white">
            <p class="text-sm opacity-80">Top Lead Generator</p>
            <p class="text-lg font-bold">{{ $kpis['top_leads'] }}</p>
            <p class="text-xs opacity-80">{{ $kpis['top_leads_count'] }} leads</p>
        </div>

        <div class="card bg-pink-500 text-white">
            <p class="text-sm opacity-80">Revenue (YTD)</p>
            <p class="text-2xl font-bold">€{{ number_format($kpis['revenue_ytd']) }}</p>
            <p class="text-xs opacity-80">dummy for now</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <h3 class="card-title">Deals Closed by Agent</h3>
                <span class="text-xs text-gray-400">performance</span>
            </div>
            <canvas id="chartDealsByAgent" height="120"></canvas>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <h3 class="card-title">Leads by Agent</h3>
                <span class="text-xs text-gray-400">pipeline</span>
            </div>
            <canvas id="chartLeadsByAgent" height="120"></canvas>
        </div>
    </div>

    {{-- Leaderboard Table --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title">Agent Leaderboard</h3>
            <span class="text-xs text-gray-400">dummy structure (replace with DB later)</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Agent</th>
                    <th>Leads</th>
                    <th>Deals Closed</th>
                    <th>Conversion</th>
                    <th>Revenue</th>
                    <th>Avg Deal</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($agentsTable as $a)
                    <tr>
                        <td class="font-medium">{{ $a['name'] }}</td>
                        <td>{{ $a['leads'] }}</td>
                        <td>{{ $a['deals'] }}</td>
                        <td>{{ $a['conversion'] }}%</td>
                        <td>€{{ number_format($a['revenue']) }}</td>
                        <td>€{{ number_format($a['avg_deal']) }}</td>
                        <td>
                            @php
                                $badge = match($a['status']) {
                                    'Active' => 'badge-success',
                                    'Low Activity' => 'badge-warning',
                                    'Inactive' => 'badge-danger',
                                    default => 'badge-light'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $a['status'] }}</span>
                        </td>
                        <td>
                            <a href="{{ $a['url'] }}" class="btn btn-sm btn-outline">
                                View
                            </a>
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

    const deals = @json($chartDealsByAgent ?? ['labels'=>[], 'values'=>[]]);
    const leads = @json($chartLeadsByAgent ?? ['labels'=>[], 'values'=>[]]);

    const el1 = document.getElementById('chartDealsByAgent');
    const el2 = document.getElementById('chartLeadsByAgent');

    if (el1) {
        new Chart(el1, {
            type: 'bar',
            data: { labels: deals.labels, datasets: [{ label: 'Deals', data: deals.values }] },
            options: { responsive: true }
        });
    }

    if (el2) {
        new Chart(el2, {
            type: 'bar',
            data: { labels: leads.labels, datasets: [{ label: 'Leads', data: leads.values }] },
            options: { responsive: true }
        });
    }
})();
</script>
