<div class="d-flex align-items-start justify-content-between mb-3">
    <div>
        <h3 class="mb-1">Inbox</h3>
        <div class="text-muted">Incoming website form submissions grouped by category.</div>
    </div>
</div>

{{-- KPI row --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Total Submissions</div>
                <div class="fs-3 fw-bold">{{ $kpis['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">This Category</div>
                <div class="fs-3 fw-bold">{{ $kpis['category'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Last 7 Days</div>
                <div class="fs-3 fw-bold">{{ $kpis['last_7'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Unread</div>
                <div class="fs-3 fw-bold">{{ $kpis['unread'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left categories --}}
    <div class="col-12 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body p-2">
                <div class="fw-semibold px-2 pt-2 pb-1">Categories</div>

                <div class="list-group list-group-flush">
                    @foreach($categories as $key => $label)
                        <a href="#"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ ($selectedCategory === $key) ? 'active' : '' }}"
                           data-inbox-category="{{ $key }}">
                            <span>{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Right table --}}
    <div class="col-12 col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">{{ $categories[$selectedCategory] ?? 'Inbox' }}</h5>
                    <div class="text-muted small">Showing {{ count($filtered ?? []) }} result(s)</div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                        <tr>
                            <th style="width:140px;">Reference</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width:180px;">Submitted</th>
                            <th style="width:120px;" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse(($filtered ?? []) as $row)
                            <tr>
                                <td class="fw-semibold text-warning">{{ $row['reference'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['email'] }}</td>
                                <td>{{ $row['submitted'] }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" title="View" disabled>
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" title="Delete" disabled>
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No submissions found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-muted small mt-2">
                    Next: connect these rows to your real inbox tables and wire View/Delete.
                </div>
            </div>
        </div>
    </div>
</div>
