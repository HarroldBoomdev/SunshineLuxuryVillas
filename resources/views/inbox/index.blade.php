@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">Inbox</h1>

  <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
    {{-- LEFT TABS --}}
    <aside class="md:col-span-3">
      <nav class="space-y-1 bg-white rounded-xl border border-gray-200 p-2">
        @foreach($tabs as $slug => $label)
          <a href="{{ route('inbox.index', ['type' => $slug]) }}"
             class="block px-3 py-2 rounded-lg text-sm
                    {{ $slug === $type ? 'bg-amber-100 text-amber-800 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
            {{ $label }}
          </a>
        @endforeach
      </nav>
    </aside>

    {{-- RIGHT CONTENT --}}
    <main class="md:col-span-9">
      <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-4 py-3 border-b flex justify-between">
          <div class="text-lg font-medium">{{ $tabs[$type] ?? 'Inbox' }}</div>
        </div>

        <div class="p-0 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
              <tr>
                <!-- <th class="px-4 py-3">#</th> -->
                <th class="px-4 py-3">Reference</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Submitted</th>
                <th class="px-4 py-3 text-right">Actions</th>
              </tr>
            </thead>

            <tbody class="divide-y">
              @forelse ($submissions as $row)
                <tr>
                  <!-- <td class="px-4 py-3">{{ $row->id }}</td> -->

                  {{-- CLICKABLE REFERENCE -> opens modal --}}
                  <td class="px-4 py-3">
                    @if($row->reference)
                      <button type="button"
                              class="text-amber-700 hover:underline font-medium"
                              onclick="SLV.openPropertyModal('{{ addslashes($row->reference) }}')">
                        {{ $row->reference }}
                      </button>
                    @else
                      —
                    @endif
                  </td>

                  <td class="px-4 py-3">{{ $row->name ?? '—' }}</td>
                  <td class="px-4 py-3">{{ $row->email ?? '—' }}</td>
                  <td class="px-4 py-3">{{ $row->created_at?->format('Y-m-d H:i') }}</td>
                  <td class="px-4 py-3 text-right space-x-3">
                    {{-- View button --}}
                    <a href="{{ route('inbox.show', $row->id) }}"
                    class="text-amber-600 hover:text-amber-800"
                    title="View">
                        <i class="fas fa-eye"></i>
                    </a>

                    {{-- Delete button --}}
                    <form action="{{ route('inbox.destroy', $row->id) }}"
                        method="POST"
                        class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this submission?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>

                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    No submissions found for <strong>{{ $tabs[$type] }}</strong>.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="px-4 py-3 border-t">
          {{ $submissions->links() }}
        </div>
      </div>
    </main>
  </div>
</div>

{{-- =========================
     PROPERTY DETAILS MODAL
========================= --}}
<div id="propertyModal"
     class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[80vh] overflow-hidden">
    <div class="flex items-center justify-between border-b px-4 py-3">
      <h2 id="propertyModalTitle" class="text-lg font-semibold">Property</h2>
      <button type="button" class="text-gray-500 hover:text-gray-700" onclick="SLV.closePropertyModal()">
        &times;
      </button>
    </div>

    {{-- Scrollable body --}}
    <div id="propertyModalBody" class="p-4 overflow-y-auto" style="max-height: calc(80vh - 56px);">
      <p class="text-gray-500">Loading…</p>
    </div>
  </div>
</div>

{{-- =========================
     PAGE SCRIPTS
========================= --}}
<script>
  window.SLV = window.SLV || {};

  (function () {
    const API_BASE = @json(url('/api')); // -> e.g. https://api.sunshineluxuryvillas.co.uk/api

    const modal  = document.getElementById('propertyModal');
    const title  = document.getElementById('propertyModalTitle');
    const bodyEl = document.getElementById('propertyModalBody');

    function show()  { modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function hide()  { modal.classList.add('hidden');   modal.classList.remove('flex'); }

    // Close on ESC / backdrop click
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
    modal.addEventListener('click', (e) => { if (e.target === modal) hide(); });

    // Render helper – keeps HTML minimal but readable
    function renderProperty(p) {
      const title = p?.title || p?.name || 'No Title';
      const desc  = p?.description || p?.details || '';
      const url   = p?.url || p?.permalink || null;

      const rows = [
        ['Location',  p?.location],
        ['Type',      p?.type],
        ['Bedrooms',  p?.bedrooms],
        ['Bathrooms', p?.bathrooms],
        ['Plot',      p?.plot],
        ['Covered',   p?.covered],
      ].filter(([_, v]) => v != null && String(v).trim() !== '');

      const rowsHtml = rows.map(([k, v]) =>
        `<div class="flex py-1"><div class="w-32 text-gray-500">${k}:</div><div class="flex-1">${v}</div></div>`
      ).join('');

      const linkHtml = url
        ? `<div class="mt-3"><a href="${url}" target="_blank" class="text-amber-700 hover:underline">Open property page</a></div>`
        : '';

      return `
        <h3 class="text-xl font-semibold mb-2">${title}</h3>
        ${rowsHtml ? `<div class="mb-4 text-sm">${rowsHtml}</div>` : ''}
        ${desc ? `<div class="prose prose-sm max-w-none whitespace-pre-line">${desc}</div>` : '<div class="text-gray-500">No description available.</div>'}
        ${linkHtml}
      `;
    }

    async function fetchByReference(ref) {
      // Adjust this endpoint to match your API:
      // Route example in routes/api.php:
      // Route::get('/properties/by-reference/{ref}', [PropertyController::class, 'byReference']);
      const url = `${API_BASE}/properties/by-reference/${encodeURIComponent(ref)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error('Not found');
      return res.json();
    }

    SLV.openPropertyModal = async function (ref) {
      title.textContent = `Property ${ref}`;
      bodyEl.innerHTML = '<p class="text-gray-500">Loading…</p>';
      show();

      try {
        const data = await fetchByReference(ref);
        bodyEl.innerHTML = renderProperty(data);
      } catch (err) {
        console.error(err);
        bodyEl.innerHTML = `
          <div class="text-red-600">Unable to load property details for <strong>${ref}</strong>.</div>
          <div class="text-gray-500 mt-2">Please verify the reference exists on the Properties API.</div>
        `;
      }
    };

    SLV.closePropertyModal = hide;
  })();



</script>
@endsection
