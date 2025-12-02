<div class="card mt-3">
    <div class="card-header">
        <strong>Property Activity Logs</strong>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reference</th>
                    <th>Title</th>
                    <th>Activity</th>
                    <th>Price (€)</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Deleted At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $p)
                    @php
                        if ($p->deleted_at) {
                            $activity = 'Deleted';
                        } elseif ($p->updated_at && $p->updated_at->gt($p->created_at)) {
                            $activity = 'Updated';
                        } else {
                            $activity = 'Created';
                        }
                    @endphp
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->reference }}</td>
                        <td>{{ $p->title }}</td>
                        <td>{{ $activity }}</td>
                        <td>
                            @if($p->price)
                                €{{ number_format($p->price, 2) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $p->created_at }}</td>
                        <td>{{ $p->updated_at }}</td>
                        <td>{{ $p->deleted_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No property activity found yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
    </div>
</div>
