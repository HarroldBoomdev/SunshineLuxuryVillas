@extends('layouts.app')

@section('content')
<div class="container">
  @include('layouts.newButton')

  <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h1 class="m-0">Banks</h1>

    {{-- Dropdown: All Banks + specific banks --}}
    <form method="GET" action="{{ route('banks.index') }}" class="d-flex" style="min-width:360px;">
        <select name="bank_id" class="form-select" onchange="this.form.submit()" style="color:#111;background:#fff;">
            <option value="">— All Banks —</option>
            @foreach($bankOptions as $opt)
            <option value="{{ $opt->id }}" {{ (int)request('bank_id') === (int)$opt->id ? 'selected' : '' }}>
                {{ $opt->name ?? ('Bank #'.$opt->id) }}
            </option>
            @endforeach
        </select>
    </form>

  </div>

  @if($mode === 'all')
    {{-- MODE A: Full Banks Table --}}
    <p class="text-muted" style="margin-top:-6px;">
      {{ $banks->total() }} results
    </p>

    <div class="overflow-x-auto">
      <table class="table table-striped table-hover">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 border" style="width:140px;">Reference</th>
            <th class="px-4 py-2 border">Name</th>
            <th class="px-4 py-2 border">Address</th>
            <th class="px-4 py-2 border" style="width:160px;">Telephone</th>
            <th class="px-4 py-2 border" style="width:140px;">Mobile</th>
            <th class="px-4 py-2 border" style="width:110px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($banks as $b)
            <tr>
              <td class="px-4 py-2 border">{{ $b->reference }}</td>
              <td class="px-4 py-2 border">{{ $b->name }}</td>
              <td class="px-4 py-2 border">{{ $b->address }}</td>
              <td class="px-4 py-2 border">{{ $b->telephone }}</td>
              <td class="px-4 py-2 border">{{ $b->mobile }}</td>
              <td class="px-4 py-2 border">
                <a href="{{ route('banks.index', ['bank_id' => $b->id]) }}" class="btn btn-sm btn-primary">
                  View Links
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No banks found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center">
      {{ $banks->withQueryString()->links() }}
    </div>

  @else
    {{-- MODE B: Selected Bank Details + Linked Properties --}}
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3"><strong>Reference:</strong> {{ $selectedBank->reference ?? '—' }}</div>
          <div class="col-md-5"><strong>Address:</strong> {{ $selectedBank->address ?? '—' }}</div>
          <div class="col-md-2"><strong>Telephone:</strong> {{ $selectedBank->telephone ?? '—' }}</div>
          <div class="col-md-2"><strong>Mobile:</strong> {{ $selectedBank->mobile ?? '—' }}</div>
        </div>
      </div>
    </div>

    <h6 class="mb-2">Linked Properties</h6>
    <p class="text-muted" style="margin-top:-6px;">
      {{ $links->total() }} results
    </p>

    <div class="overflow-x-auto">
      <table class="table table-striped table-hover">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 border" style="width:260px;">Property Reference</th>
            <th class="px-4 py-2 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($links as $row)
            <tr>
              <td class="px-4 py-2 border">{{ $row->property_reference }}</td>
              <td class="px-4 py-2 border">
                {{-- Link to property show if available --}}
                {{-- <a href="{{ route('properties.showByRef', $row->property_reference) }}" class="btn btn-sm btn-primary">View</a> --}}
              </td>
            </tr>
          @empty
            <tr><td colspan="2" class="text-center text-muted">No properties linked.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center">
      {{ $links->withQueryString()->links() }}
    </div>
  @endif
</div>
@endsection
