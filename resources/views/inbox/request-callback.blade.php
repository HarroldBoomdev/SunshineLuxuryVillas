@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Request a Callback</h2>

  @if($rows->count() === 0)
    <div class="alert alert-info">No entries yet.</div>
  @else
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Created</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Reference</th>
            <th>Comments</th>
            <th>URL</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $r)
            @php
              $p = is_array($r->payload) ? $r->payload : (json_decode($r->payload ?? '[]', true) ?: []);
            @endphp
            <tr>
              <td>{{ $r->id }}</td>
              <td>{{ $r->created_at?->timezone('Asia/Manila')->format('Y-m-d H:i') }}</td>
              <td>{{ $r->name ?? '—' }}</td>
              <td>{{ $r->phone ?? '—' }}</td>
              <td>{{ $r->email ?? '—' }}</td>
              <td>{{ $r->reference ?? '—' }}</td>
              <td>{{ $p['comments'] ?? $p['message'] ?? '—' }}</td>
              <td>
                @if(!empty($p['url']))
                  <a href="{{ $p['url'] }}" target="_blank" rel="noopener">Open</a>
                @else
                  —
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- pagination --}}
    <div class="mt-3">
      {{ $rows->withQueryString()->links() }}
    </div>
  @endif
</div>
@endsection
