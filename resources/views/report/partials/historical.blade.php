@extends('layouts.app')

@section('title', 'Historical Listings Report')

@section('content')
<div class="container mx-auto px-4 py-6 print:px-0">
  {{-- Page header --}}
  <div class="flex items-center justify-between gap-3 mb-6 print:mb-3">
    <h1 class="text-2xl font-semibold">Historical Listings</h1>

    <div class="flex items-center gap-2 print:hidden">
      {{-- keep query string on export links --}}
      @php $q = request()->query(); @endphp

      <a href="{{ route('reports.historical.csv', $q) }}"
         class="inline-flex items-center rounded-lg border px-3 py-2 text-sm hover:bg-gray-50">
        Download CSV
      </a>

      <a href="{{ route('reports.historical.pdf', $q) }}"
         class="inline-flex items-center rounded-lg border px-3 py-2 text-sm hover:bg-gray-50">
        Download PDF
      </a>

      <button type="button" onclick="window.print()"
        class="inline-flex items-center rounded-lg bg-gray-900 text-white px-3 py-2 text-sm hover:bg-gray-800">
        Print Preview
      </button>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-6 print:hidden">
    <div class="md:col-span-2">
      <label class="text-xs text-gray-500">Date range</label>
      <input type="text" name="range" value="{{ request('range','this-year') }}"
             class="w-full rounded-lg border px-3 py-2" placeholder="YYYY-MM-DD to YYYY-MM-DD">
      <p class="text-[11px] text-gray-400 mt-1">Examples: <code>2024-01-01 to 2024-12-31</code> or <code>last-12m</code></p>
    </div>

    <div>
      <label class="text-xs text-gray-500">Agent</label>
      <select name="agent" class="w-full rounded-lg border px-3 py-2">
        <option value="">All</option>
        @foreach($filters['agents'] ?? [] as $a)
          <option value="{{ $a->id }}" @selected(request('agent')==$a->id)>{{ $a->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs text-gray-500">Region / Town</label>
      <select name="region" class="w-full rounded-lg border px-3 py-2">
        <option value="">All</option>
        @foreach($filters['regions'] ?? [] as $r)
          <option value="{{ $r }}" @selected(request('region')==$r)>{{ $r }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs text-gray-500">Type</label>
      <select name="type" class="w-full rounded-lg border px-3 py-2">
        <option value="">All</option>
        @foreach($filters['types'] ?? [] as $t)
          <option value="{{ $t }}" @selected(request('type')==$t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs text-gray-500">Status</label>
      <select name="status" class="w-full rounded-lg border px-3 py-2">
        <option value="">All</option>
        @foreach($filters['statuses'] ?? ['sold','expired','withdrawn'] as $s)
          <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-6 flex items-end gap-2">
      <button class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">Apply</button>
      <a href="{{ route('reports.historical') }}" class="text-sm text-gray-600 hover:underline">Reset</a>
    </div>
  </form>

  {{-- KPI cards --}}
  <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
    @php
      $cards = [
        ['label'=>'Total Listings','value'=>number_format($stats['total'] ?? 0)],
        ['label'=>'Sold','value'=>number_format($stats['sold'] ?? 0)],
        ['label'=>'Expired','value'=>number_format($stats['expired'] ?? 0)],
        ['label'=>'Withdrawn','value'=>number_format($stats['withdrawn'] ?? 0)],
        ['label'=>'Avg. Days on Market','value'=>number_format($stats['avg_days'] ?? 0)],
      ];
    @endphp
    @foreach($cards as $c)
      <div class="rounded-2xl border bg-white p-4">
        <div class="text-sm text-gray-500">{{ $c['label'] }}</div>
        <div class="mt-1 text-2xl font-semibold">{{ $c['value'] }}</div>
      </div>
    @endforeach
  </div>

  {{-- Table --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">Ref</th>
            <th class="px-4 py-3 text-left">Property</th>
            <th class="px-4 py-3 text-left">Location</th>
            <th class="px-4 py-3 text-left">Agent</th>
            <th class="px-4 py-3 text-left">Listed</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-right">Days</th>
            <th class="px-4 py-3 text-right">Final Value (€)</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($listings as $row)
            @php
              $status = strtolower($row->status);
              $rowBg = $status==='sold' ? 'bg-green-50' : ($status==='expired' ? 'bg-red-50' : ($status==='withdrawn' ? 'bg-gray-50' : ''));
            @endphp
            <tr class="{{ $rowBg }}">
              <td class="px-4 py-3 font-medium">{{ $row->reference }}</td>
              <td class="px-4 py-3">{{ $row->title }}</td>
              <td class="px-4 py-3">{{ $row->location }}</td>
              <td class="px-4 py-3">{{ $row->agent_name }}</td>
              <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($row->listed_at)->format('Y-m-d') }}</td>
              <td class="px-4 py-3 capitalize">{{ $row->status }}</td>
              <td class="px-4 py-3 text-right">{{ $row->days_on_market }}</td>
              <td class="px-4 py-3 text-right">{{ number_format($row->final_value, 0) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-10 text-center text-gray-500">No results for the selected filters.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- simple footer / pagination slot --}}
    @if(method_exists($listings,'links'))
      <div class="p-3 print:hidden">
        {{ $listings->appends(request()->query())->links() }}
      </div>
    @endif
  </div>

  {{-- Charts --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">
    <div class="rounded-2xl border bg-white p-4">
      <h3 class="text-sm font-medium mb-2">Listings per Year</h3>
      <canvas id="chartYear"></canvas>
    </div>
    <div class="rounded-2xl border bg-white p-4">
      <h3 class="text-sm font-medium mb-2">Status Distribution</h3>
      <canvas id="chartStatus"></canvas>
    </div>
    <div class="rounded-2xl border bg-white p-4">
      <h3 class="text-sm font-medium mb-2">Average Listing Value (€)</h3>
      <canvas id="chartValue"></canvas>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Print-friendly */
  @media print {
    .print\:hidden { display: none !important; }
    .container { max-width: 100% !important; }
    a[href]:after { content: ""; } /* remove link URLs in print */
    table { page-break-inside: auto; }
    tr    { page-break-inside: avoid; page-break-after: auto; }
    .rounded-2xl, .border { border-color: #e5e7eb !important; }
    .bg-green-50, .bg-red-50, .bg-gray-50 { background: #fff !important; } /* flatten colors for ink */
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Data from controller
  const yearly = @json($yearlyCounts ?? []);           // [{year:2021,count:120}, ...]
  const status = @json($statusBreakdown ?? []);        // [{status:'sold',count:50}, ...]
  const valueTrend = @json($valueTrend ?? []);         // [{label:'2024-Q1',avg:230000}, ...]

  // Yearly bar
  new Chart(document.getElementById('chartYear'), {
    type: 'bar',
    data: {
      labels: yearly.map(x => x.year),
      datasets: [{ label: 'Listings', data: yearly.map(x => x.count) }]
    },
    options: { responsive: true, plugins: { legend: { display:false } } }
  });

  // Status pie
  new Chart(document.getElementById('chartStatus'), {
    type: 'pie',
    data: {
      labels: status.map(x => x.status.charAt(0).toUpperCase() + x.status.slice(1)),
      datasets: [{ data: status.map(x => x.count) }]
    },
    options: { responsive: true }
  });

  // Value trend line
  new Chart(document.getElementById('chartValue'), {
    type: 'line',
    data: {
      labels: valueTrend.map(x => x.label),
      datasets: [{ label: 'Avg €', data: valueTrend.map(x => x.avg), tension: .3, fill:false }]
    },
    options: { responsive: true, plugins: { legend: { display:false } } }
  });
</script>
@endpush
