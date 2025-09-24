@extends('layouts.app')

@section('content')
<div class="container">
    @include('layouts.newButton')
    <div class="card p-3 mb-4">

        <form method="GET" action="{{ route('developers.index') }}" class="row g-2 mb-4">
            <!-- Reference -->
            <div class="col-md-3">
                <input type="text" name="reference" class="form-control" placeholder="Reference" value="{{ request('reference') }}">
            </div>

            <!-- Name -->
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
            </div>

            <!-- Email -->
            <div class="col-md-3">
                <input type="text" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
            </div>

            <!-- Buttons -->
            <div class="col-md-3 d-flex gap-2 align-items-start">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('developers.index') }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route('developers.export', request()->query()) }}" class="btn btn-success">Download Excel</a>
            </div>
        </form>

    </div>

    <h1 class="mt-4">Developers</h1>
    <p class="text-muted">{{ $developers->total() ?? $developers->count() }} results</p> <!-- Handles both paginated and non-paginated scenarios -->

    <div class="overflow-x-auto">
        <table class="table table-striped table-hover">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">Reference</th>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Phone</th>
                    <th class="px-4 py-2 border">Banks</th>
                    <th class="px-4 py-2 border">Website</th>
                    <th class="px-4 py-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($developers as $developer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $developer->reference }}</td>
                        <td class="px-4 py-2 border">{{ $developer->name }}</td>
                        <td class="px-4 py-2 border">{{ $developer->email }}</td>
                        <td class="px-4 py-2 border">{{ $developer->phone }}</td>
                        <td class="px-4 py-2 border">{{ $developer->phone }}</td>
                        <td class="px-4 py-2 border">
                            <a href="http://{{ $developer->website }}" target="_blank" class="text-blue-500 underline">
                                {{ $developer->website }}
                            </a>
                        </td>
                        <td class="px-4 py-2 border">
                            @can('developer.view')
                                <a href="{{ route('developers.details', $developer->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                            @endcan

                            @can('developer.edit')
                                <a href="{{ route('developers.edit', $developer->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endcan

                            @can('developer.delete')
                                <form action="{{ route('developers.destroy', $developer->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this developer?')" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No developers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if (method_exists($developers, 'links'))
        <div class="d-flex justify-content-center mt-3">
            {{ $developers->links() }}
        </div>
    @endif
</div>
@endsection
