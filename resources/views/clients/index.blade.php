@extends('layouts.app')

@section('content')
<div class="container">
@include('layouts.newButton')
<div class="card p-3 mb-4">
    <form id="clientSearchForm" method="GET" action="{{ route('clients.index') }}">
        <div class="row g-3 align-items-center">

            <!-- First Name -->
            <div class="col-md-3">
                <input type="text" name="first_name" class="form-control" placeholder="First Name"
                    value="{{ request('first_name') }}" oninput="submitClientForm()">
            </div>

            <!-- Last Name -->
            <div class="col-md-3">
                <input type="text" name="last_name" class="form-control" placeholder="Last Name"
                    value="{{ request('last_name') }}" oninput="submitClientForm()">
            </div>

            <!-- Email -->
            <div class="col-md-3">
                <input type="email" name="email" class="form-control" placeholder="Email"
                    value="{{ request('email') }}" oninput="submitClientForm()">
            </div>

            <!-- Status -->
            <div class="col-md-3">
                <select name="status" class="form-control" onchange="submitClientForm()">
                    <option value="">-- Status --</option>
                    <option value="TRUE" {{ request('status') == 'TRUE' ? 'selected' : '' }}>Active</option>
                    <option value="FALSE" {{ request('status') == 'FALSE' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Price Range Label -->
            <div class="col-md-12">
                <label class="form-label d-block">Price Range:
                    <span id="priceRangeLabel">{{ request('min_price', 10000) }} - {{ request('max_price', 1000000) }}</span>
                </label>
            </div>

            <!-- Min Price Slider -->
            <div class="col-md-6">
                <label for="min_price" class="form-label">Min Price</label>
                <input type="range" class="form-range" name="min_price_slider" id="minPriceSlider"
                    min="10000" max="1000000" step="10000" value="{{ request('min_price', 10000) }}"
                    oninput="updatePriceSliders()" onchange="submitClientForm()">
                <input type="hidden" name="min_price" id="min_price" value="{{ request('min_price', 10000) }}">
            </div>

            <!-- Max Price Slider -->
            <div class="col-md-6">
                <label for="max_price" class="form-label">Max Price</label>
                <input type="range" class="form-range" name="max_price_slider" id="maxPriceSlider"
                    min="10000" max="1000000" step="10000" value="{{ request('max_price', 1000000) }}"
                    oninput="updatePriceSliders()" onchange="submitClientForm()">
                <input type="hidden" name="max_price" id="max_price" value="{{ request('max_price', 1000000) }}">
            </div>

            <!-- Buttons -->
            <div class="col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route('clients.export', request()->query()) }}" class="btn btn-success">Download Excel</a>
            </div>
        </div>
    </form>


</div>

    <h1 class="mt-4">Clients</h1>
    <p class="text-muted">{{ $clients->total() }} results</p>

    <div class="overflow-x-auto">
        <table class="table table-striped table-hover">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">Reference</th>
                    <th class="px-4 py-2 border">First Name</th>
                    <th class="px-4 py-2 border">Last Name</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Mobile</th>
                    <th class="px-4 py-2 border">Min Price</th>
                    <th class="px-4 py-2 border">Max Price</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">Action</th> <!-- New Action column -->
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    <tr>
                        <td class="px-4 py-2 border">{{ $client->id }}</td>
                        <td class="px-4 py-2 border">{{ $client->first_name }}</td>
                        <td class="px-4 py-2 border">{{ $client->last_name }}</td>
                        <td class="px-4 py-2 border">{{ $client->email }}</td>
                        <td class="px-4 py-2 border">{{ $client->mobile }}</td>
                        <td class="px-4 py-2 border">{{ $client->MinimumPrice }}</td>
                        <td class="px-4 py-2 border">{{ $client->MaximumPrice }}</td>
                        <td class="px-4 py-2 border">{{ $client->Status }}</td>

                        <td class="px-4 py-2 border">
                            @can('client.contact.email')
                                @if($client->email)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-dark"
                                        title="Send Property Recommendations"
                                        onclick="openRecommendationsModal({{ $client->id }})">
                                        <i class="fa fa-list-check"></i>
                                    </button>
                                @endif
                            @endcan
                            @can('client.view')
                                <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                            @endcan

                            @can('client.contact.email')
                                @if($client->email)
                                    <a href="mailto:{{ $client->email }}" class="btn btn-sm btn-primary" title="Email">
                                        <i class="fa fa-envelope"></i>
                                    </a>
                                @endif
                            @endcan

                            @can('client.contact.whatsapp')
                                @if($client->mobile)
                                    @php
                                        $whatsappNumber = preg_replace('/[^0-9]/', '', $client->mobile);
                                    @endphp
                                    <a href="https://wa.me/{{ $whatsappNumber }}" class="btn btn-sm btn-success" title="WhatsApp" target="_blank">
                                        <i class="fa fa-whatsapp"></i>
                                    </a>
                                @endif
                            @endcan

                            @can('client.edit')
                                <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endcan

                            @can('client.delete')
                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No clients found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $clients->links() }}
    </div>
    <!-- Recommendations Modal -->
<div class="modal fade" id="recommendationsModal" tabindex="-1" aria-labelledby="recommendationsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="recommendationsModalLabel">Property Recommendations</h5>
          <div class="text-muted small" id="recClientMeta"></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
          <button class="btn btn-sm btn-outline-primary" type="button" onclick="recSelectAll(true)">Select all</button>
          <button class="btn btn-sm btn-outline-secondary" type="button" onclick="recSelectAll(false)">Unselect all</button>
          <span class="ms-auto small text-muted" id="recCountLabel">0 properties</span>
        </div>

        <div id="recLoading" class="text-center py-4 d-none">
          <div class="spinner-border" role="status"></div>
          <div class="mt-2 text-muted">Loading matching properties...</div>
        </div>

        <div id="recEmpty" class="alert alert-warning d-none">
          No matching properties found for this client budget.
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th style="width:40px" class="text-center">
                  <input type="checkbox" id="recMasterCheckbox" onchange="recMasterToggle(this)">
                </th>
                <th style="width:90px">Photo</th>
                <th>Title</th>
                <th style="width:140px">Ref</th>
                <th style="width:160px">Location</th>
                <th style="width:140px">Price</th>
                <th style="width:160px">Link</th>
              </tr>
            </thead>
            <tbody id="recTbody">
              <!-- filled by JS -->
            </tbody>
          </table>
        </div>

      </div>

      <div class="modal-footer">
        <div class="me-auto small text-muted" id="recSelectedLabel">0 selected</div>

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        <button type="button" class="btn btn-success" id="recSendBtn" onclick="sendRecommendations()">
          <i class="fa fa-paper-plane"></i> Send
        </button>
      </div>

    </div>
  </div>
</div>

</div>

<script>
    function updatePriceSliders() {
        const minVal = document.getElementById('minPriceSlider').value;
        const maxVal = document.getElementById('maxPriceSlider').value;

        // Prevent overlap
        if (parseInt(minVal) > parseInt(maxVal)) {
            document.getElementById('minPriceSlider').value = maxVal;
        }

        document.getElementById('min_price').value = document.getElementById('minPriceSlider').value;
        document.getElementById('max_price').value = document.getElementById('maxPriceSlider').value;

        document.getElementById('priceRangeLabel').textContent =
            `${document.getElementById('min_price').value} - ${document.getElementById('max_price').value}`;
    }

    function submitClientForm() {
        document.getElementById('clientSearchForm').submit();
    }

    let recClientId = null;
  let recItems = []; // loaded properties

  function openRecommendationsModal(clientId) {
    recClientId = clientId;

    // reset UI
    document.getElementById('recTbody').innerHTML = '';
    document.getElementById('recEmpty').classList.add('d-none');
    document.getElementById('recLoading').classList.remove('d-none');
    document.getElementById('recCountLabel').textContent = '0 properties';
    document.getElementById('recSelectedLabel').textContent = '0 selected';
    document.getElementById('recMasterCheckbox').checked = false;

    // show modal
    const modalEl = document.getElementById('recommendationsModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // load
    fetch(`{{ url('/clients') }}/${clientId}/recommendations`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
      recItems = data.properties || [];

      document.getElementById('recClientMeta').textContent =
        `${data.client_name} • Budget: €${data.min_price} - €${data.max_price} • ${data.client_email}`;

      document.getElementById('recLoading').classList.add('d-none');

      if (!recItems.length) {
        document.getElementById('recEmpty').classList.remove('d-none');
        document.getElementById('recCountLabel').textContent = '0 properties';
        return;
      }

      document.getElementById('recCountLabel').textContent = `${recItems.length} properties`;

      const tbody = document.getElementById('recTbody');
      tbody.innerHTML = recItems.map(p => {
        const safeTitle = (p.title || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const safeLoc = (p.location || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const safeRef = (p.reference || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');

        return `
          <tr>
            <td class="text-center">
              <input type="checkbox" class="rec-checkbox" value="${p.id}" onchange="recUpdateSelectedCount()">
            </td>
            <td>
              <img src="${p.photo}" style="width:80px;height:60px;object-fit:cover;border-radius:6px"
                   onerror="this.onerror=null;this.src='{{ asset('images/no-image.jpg') }}'">
            </td>
            <td>${safeTitle}</td>
            <td>${safeRef}</td>
            <td>${safeLoc}</td>
            <td>€${Number(p.price || 0).toLocaleString()}</td>
            <td>
              <a href="${p.url}" target="_blank" rel="noopener" class="text-decoration-underline">
                View Further Details
              </a>
            </td>
          </tr>
        `;
      }).join('');

      recUpdateSelectedCount();
    })
    .catch(err => {
      console.error(err);
      document.getElementById('recLoading').classList.add('d-none');
      document.getElementById('recEmpty').classList.remove('d-none');
    });
  }

  function recSelectAll(select) {
    document.querySelectorAll('.rec-checkbox').forEach(cb => cb.checked = !!select);
    document.getElementById('recMasterCheckbox').checked = !!select;
    recUpdateSelectedCount();
  }

  function recMasterToggle(master) {
    recSelectAll(master.checked);
  }

  function recUpdateSelectedCount() {
    const selected = document.querySelectorAll('.rec-checkbox:checked').length;
    document.getElementById('recSelectedLabel').textContent = `${selected} selected`;

    // keep master checkbox in sync
    const all = document.querySelectorAll('.rec-checkbox').length;
    document.getElementById('recMasterCheckbox').checked = (all > 0 && selected === all);
  }

  function sendRecommendations() {
    const ids = Array.from(document.querySelectorAll('.rec-checkbox:checked')).map(x => x.value);

    if (!ids.length) {
      alert('Select at least 1 property.');
      return;
    }

    const btn = document.getElementById('recSendBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

    fetch(`{{ url('/clients') }}/${recClientId}/recommendations/send`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ property_ids: ids })
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send';

      if (!data.ok) {
        alert(data.message || 'Failed to send.');
        return;
      }

      alert('Recommendations email sent.');
      // optional: close modal
      bootstrap.Modal.getInstance(document.getElementById('recommendationsModal')).hide();
    })
    .catch(err => {
      console.error(err);
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send';
      alert('Failed to send.');
    });
  }
</script>


@endsection
