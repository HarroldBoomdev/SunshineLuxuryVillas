<div class="d-flex align-items-start justify-content-between mb-3">
    <div>
        <h3 class="mb-1">Users Activity</h3>
        <div class="text-muted">Track user actions on the site (Listings, Deals, Clients, etc.) by date and action type.</div>
    </div>

    <form method="GET" action="{{ url('/report/partials/users') }}" class="d-flex gap-2">
        <input type="hidden" name="year" value="{{ $selectedYear }}">
        <button class="btn btn-sm btn-outline-secondary" type="submit">Refresh</button>
    </form>
</div>

{{-- Filters --}}
<form method="GET" action="{{ url('/report/partials/users') }}" class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">

            <div class="col-12 col-md-2">
                <label class="form-label mb-1">Year</label>
                <select name="year" class="form-select">
                    @foreach($reportYears as $y)
                        <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label mb-1">User</label>
                <select name="user" class="form-select">
                    <option value="all" {{ ($filters['user'] ?? 'all') === 'all' ? 'selected' : '' }}>All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u['id'] }}" {{ (string)($filters['user'] ?? 'all') === (string)$u['id'] ? 'selected' : '' }}>
                            {{ $u['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label mb-1">Module</label>
                <select name="module" class="form-select">
                    @foreach($modules as $mKey => $mLabel)
                        <option value="{{ $mKey }}" {{ ($filters['module'] ?? 'all') === $mKey ? 'selected' : '' }}>{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label mb-1">Action</label>
                <select name="action" class="form-select">
                    @foreach($actions as $aKey => $aLabel)
                        <option value="{{ $aKey }}" {{ ($filters['action'] ?? 'all') === $aKey ? 'selected' : '' }}>{{ $aLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Date Range</label>
                <div class="d-flex gap-2">
                    <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
                    <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a class="btn btn-light border" href="{{ url('/report/partials/users') }}">Reset</a>
                <button class="btn btn-primary" type="submit">Apply Filters</button>
            </div>

        </div>
    </div>
</form>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Total Actions</div>
                <div class="fs-3 fw-bold">{{ $kpis['total_actions'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Listings Added</div>
                <div class="fs-3 fw-bold">{{ $kpis['listing_added'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Listings Deleted</div>
                <div class="fs-3 fw-bold">{{ $kpis['listing_deleted'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Shares</div>
                <div class="fs-3 fw-bold">{{ $kpis['shares'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Activity Table --}}
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Activity Log</h5>
            <div class="text-muted small">Showing {{ count($rows) }} result(s)</div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:170px;">Date/Time</th>
                        <th>User</th>
                        <th style="width:140px;">Module</th>
                        <th style="width:140px;">Action</th>
                        <th>Details</th>
                        <th style="width:170px;">Target</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td class="text-muted">{{ $r['created_at'] }}</td>
                            <td class="fw-semibold">{{ $r['user_name'] }}</td>
                            <td>{{ $r['module'] }}</td>
                            <td>
                                @php
                                    $badge = match($r['action']) {
                                        'create' => 'bg-success',
                                        'update' => 'bg-warning text-dark',
                                        'delete' => 'bg-danger',
                                        'share'  => 'bg-info text-dark',
                                        'login'  => 'bg-secondary',
                                        default  => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ strtoupper($r['action']) }}</span>
                            </td>
                            <td class="text-muted">{{ $r['message'] }}</td>
                            <td>
                                @if(!empty($r['target']))
                                    <span class="fw-semibold">{{ $r['target'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No activity found for your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="text-muted small mt-2">
            Next step: replace dummy rows with your real audit table (user_id, module, action, target_id, meta, created_at).
        </div>
    </div>
</div>
