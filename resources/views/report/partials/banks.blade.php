<div class="card shadow-sm border-0 mb-3">
    <div class="card-body d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="mb-1">Banks Report</h5>
            <div class="text-muted small">Bank-owned inventory overview (dummy data for now).</div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <select id="filterBank" class="form-select form-select-sm" style="min-width: 180px;">
                <option value="all">All Banks</option>
                @foreach($banks as $b)
                    <option value="{{ $b }}" @selected(($filters['bank'] ?? 'all') === $b)>{{ $b }}</option>
                @endforeach
            </select>

            <select id="filterStatus" class="form-select form-select-sm" style="min-width: 160px;">
                @foreach($statusOptions as $k => $label)
                    <option value="{{ $k }}" @selected(($filters['status'] ?? 'all') === $k)>{{ $label }}</option>
                @endforeach
            </select>

            <button id="applyBankFilters" class="btn btn-warning btn-sm">Apply Filters</button>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Bank Properties</div>
                <div class="fs-3 fw-bold">{{ $kpis['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Active Listings</div>
                <div class="fs-3 fw-bold">{{ $kpis['active'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Website Live</div>
                <div class="fs-3 fw-bold">{{ $kpis['live'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Top Bank (by count)</div>
                <div class="fw-bold">{{ $kpis['top_bank'] ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Properties per Bank</h6>
                    <span class="text-muted small">dummy</span>
                </div>
                <div style="height: 320px;">
                    <canvas id="bankCountChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Average Price by Bank</h6>
                    <span class="text-muted small">dummy</span>
                </div>
                <div style="height: 320px;">
                    <canvas id="bankAvgPriceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Bank Inventory Summary</h6>
            <span class="text-muted small">dummy</span>
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                <tr class="text-muted small">
                    <th>Bank</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Active</th>
                    <th class="text-end">Website Live</th>
                    <th class="text-end">Avg Price</th>
                    <th class="text-end">Min</th>
                    <th class="text-end">Max</th>
                    <th>Top Town</th>
                    <th>Top Type</th>
                    <th class="text-end">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($banksTable as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r['bank'] }}</td>
                        <td class="text-end">{{ $r['total'] }}</td>
                        <td class="text-end">{{ $r['active'] }}</td>
                        <td class="text-end">{{ $r['website_live'] }}</td>
                        <td class="text-end">€{{ number_format($r['avg_price']) }}</td>
                        <td class="text-end">€{{ number_format($r['min_price']) }}</td>
                        <td class="text-end">€{{ number_format($r['max_price']) }}</td>
                        <td>{{ $r['top_town'] }}</td>
                        <td>{{ $r['top_type'] }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="{{ $r['url'] }}">View properties</a>
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
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded.');
        return;
    }

    const chartByBank = @json($chartByBank);
    const chartAvgPrice = @json($chartAvgPrice);

    // Bar: count per bank
    const countEl = document.getElementById('bankCountChart');
    if (countEl && chartByBank?.labels?.length) {
        new Chart(countEl, {
            type: 'bar',
            data: {
                labels: chartByBank.labels,
                datasets: [{ label: 'Properties', data: chartByBank.values }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } }
            }
        });
    }

    // Bar: avg price
    const avgEl = document.getElementById('bankAvgPriceChart');
    if (avgEl && chartAvgPrice?.labels?.length) {
        new Chart(avgEl, {
            type: 'bar',
            data: {
                labels: chartAvgPrice.labels,
                datasets: [{ label: 'Avg Price (€)', data: chartAvgPrice.values }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    y: {
                        ticks: {
                            callback: (v) => '€' + Number(v).toLocaleString()
                        }
                    }
                }
            }
        });
    }

    // Filter button: just reload the partial URL with querystring (same pattern as your other reports)
    const btn = document.getElementById('applyBankFilters');
    btn?.addEventListener('click', () => {
        const bank = document.getElementById('filterBank')?.value || 'all';
        const status = document.getElementById('filterStatus')?.value || 'all';
        const url = `/report/partials/banks?bank=${encodeURIComponent(bank)}&status=${encodeURIComponent(status)}`;
        window.location.href = url;
    });
})();
</script>
