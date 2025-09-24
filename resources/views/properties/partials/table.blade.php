@foreach ($properties as $property)
    <tr>
        <td>
            <input type="checkbox" name="selected[]" value="{{ $property->id }}">
        </td>
        <td>
            @if (!empty($property->photos))
                @php $images = json_decode($property->photos, true); @endphp
                @if (!empty($images) && is_array($images))
                    <img src="{{ asset('storage/properties/' . $images[0]) }}" alt="Thumbnail" style="width: 80px;">
                @endif
            @endif
        </td>
        <td>{{ $property->reference }}</td>
        <td>{{ $property->title }}</td>
        <td>{{ $property->location ?? 'N/A' }}</td>
        <td>{{ $property->bedrooms }}</td>
        <td>€{{ number_format($property->price, 2) }}</td>
        <td>{{ $property->plot_area ?? 'N/A' }}</td>
        <td>{{ $property->built_area ?? 'N/A' }}</td>
        <td>{{ $property->bank ?? '—' }}</td>
        <td>{!! $property->website_live ? '<span class="text-green-500">&#10004;</span>' : '<span class="text-red-500">&#10008;</span>' !!}</td>
        <td class="px-4 py-2 border">
            @can('property.view')
                <a href="#"
                    class="btn btn-sm btn-info"
                    title="View"
                    onclick="openTab('#{{ $property->id }} - {{ Str::limit($property->title, 20) }}', '{{ route('properties.show', $property->id) }}'); return false;">
                    <i class="fa fa-eye"></i>
                </a>
            @endcan

            @can('property.edit')
                <a href="{{ route('properties.edit', $property->id) }}" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
            @endcan

            @can('property.delete')
                <form action="{{ route('properties.destroy', $property->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            @endcan
        </td>
    </tr>
@endforeach
