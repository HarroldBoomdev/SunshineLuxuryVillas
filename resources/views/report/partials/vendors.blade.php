<div class="card shadow-sm border-0 mb-3">
    <div class="card-body d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="mb-1">Vendors Report</h5>
            <div class="text-muted small">Vendor inventory overview (dummy data for now).</div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <select id="filterVendor" class="form-select form-select-sm" style="min-width: 220px;">
                <option value="all">All Vendors</option>
                @foreach($vendors as $v)
                    <option value="{{ $v }}" @selected(($filters['vendor'] ?? 'all') === $v)>{{ $v }}</option>
                @endforeach
            </select>

            <select id="filterStatus" class="form-select form-select-sm" style="min-width: 160px;">
                @foreach($statusOptions as $k => $label)
                    <option value="{{ $k }}" @selected(($filters['status'] ?? 'all') === $k)>{{ $label }}</option>
                @endforeach
            </select>

            <button id="applyVendorFilters" class="btn btn-warning btn-sm">Apply Filters</button>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Vendors</div>
                <div class="fs-3 fw-bold">{{ $kpis['vendors_total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Active Vendors</div>
                <div class="fs-3 fw-bold">{{ $kpis['vendors_active'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Vendor Properties</div>
                <div class="fs-3 fw-bold">{{ $kpis['properties_total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Avg / Vendor</div>
                <div class="fs-3 fw-bold">{{ $kpis['avg_per_vendor'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Top Vendors by Properties</h6>
                    <span class="text-muted small">dummy</span>
                </div>
                <div style="height: 320px;">
                    <canvas id="vendorTopChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Inventory Share</h6>
                    <span class="text-muted small">dummy</span>
                </div>
                <div style="height: 320px;">
                    <canvas id="vendorShareChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Vendor Summary</h6>
            <span class="text-muted small">dummy</span>
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                <tr class="text-muted small">
                    <th>Vendor</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Active</th>
                    <th class="text-end">Website Live</th>
                    <th class="text-end">Live Rate</th>
                    <th class="text-end">Avg Price</th>
                    <th>Top Type</th>
                    <th>Top Region</th>
                    <th class="text-end">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($vendorsTable as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r['vendor'] }}</td>
                        <td class="text-end">{{ $r['total'] }}</td>
                        <td class="text-end">{{ $r['active'] }}</td>
                        <td class="text-end">{{ $r['website_live'] }}</td>
                        <td class="text-end">
                            <span class="badge bg-primary">{{ $r['live_rate'] }}%</span>
                        </td>
                        <td class="text-end">€{{ number_format($r['avg_price']) }}</td>
                        <td>{{ $r['top_type'] }}</td>
                        <td>{{ $r['top_region'] }}</td>
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

    const top = @json($chartTopVendors);
    const share = @json($chartVendorShare);

    const topEl = document.getElementById('vendorTopChart');
    if (topEl && top?.labels?.length) {
        new Chart(topEl, {
            type: 'bar',
            data: { labels: top.labels, datasets: [{ label: 'Properties', data: top.values }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    const shareEl = document.getElementById('vendorShareChart');
    if (shareEl && share?.labels?.length) {
        new Chart(shareEl, {
            type: 'doughnut',
            data: { labels: share.labels, datasets: [{ data: share.values }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    document.getElementById('applyVendorFilters')?.addEventListener('click', () => {
        const vendor = document.getElementById('filterVendor')?.value || 'all';
        const status = document.getElementById('filterStatus')?.value || 'all';
        window.location.href = `/report/partials/vendors?vendor=${encodeURIComponent(vendor)}&status=${encodeURIComponent(status)}`;
    });
})();
</script>
