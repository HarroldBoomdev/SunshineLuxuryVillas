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
</script>


@endsection
