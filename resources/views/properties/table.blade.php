<table class="table">
    <thead>
        <tr>
            <th class="px-4 py-2 border">Thumbnail</th>
            <th class="px-4 py-2 border">Title</th>
            <th class="px-4 py-2 border">Complex</th>
            <th class="px-4 py-2 border">Country</th>
            <th class="px-4 py-2 border">Status</th>
            <th class="px-4 py-2 border">Units</th>
            <th class="px-4 py-2 border">Bedrooms</th>
            <th class="px-4 py-2 border">Bathrooms</th>
            <th class="px-4 py-2 border">Covered</th>
            <th class="px-4 py-2 border">Price</th>
            <th class="px-4 py-2 border">Website</th>
            <th class="px-4 py-2 border">Actions</th>
        </tr>
    </thead>
        <tbody>
            @forelse ($properties as $property)
                <tr>
                    <td class="px-4 py-2 border">
                    @php
                        $photos = is_string($property->photos)
                            ? explode(',', $property->photos)
                            : (is_array($property->photos) ? $property->photos : []);
                        $thumbnail = !empty($photos[0]) ? trim($photos[0]) : asset('images/placeholder.png');
                    @endphp


                        <img src="{{ $thumbnail }}" alt="Thumbnail" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td class="px-4 py-2 border">{{ $property->title ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $property->complex ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $property->country ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $property->status ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $property->units ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $property->bedrooms ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $property->bathrooms ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $property->covered ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">
                        @if($property->price)
                            €{{ number_format($property->price, 2) }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border">
                        @if($property->website)
                            <a href="{{ $property->website }}" target="_blank"><i class="fa fa-external-link text-primary"></i></a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border">
                        <a href="{{ route('properties.show', $property->id) }}" class="btn btn-sm btn-info" title="View">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="{{ route('properties.edit', $property->id) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('properties.destroy', $property->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this property?')" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">No properties found.</td>
                </tr>
            @endforelse
        </tbody>
</table>

<!-- Pagination (only show if search is empty) -->
@if(!request()->ajax())
    {{ $properties->links() }}
@endif
