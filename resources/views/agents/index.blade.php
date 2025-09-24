@extends('layouts.app')

@section('content')
<div class="container">
    @include('layouts.newButton')
    <h1 class="mt-4">Agents</h1>
    <p class="text-muted">{{ $agents->count() }} results</p> <!-- Updated for collections -->

    <div class="overflow-x-auto">
        <table class="table table-striped table-hover">
            <thead>
                <tr class="bg-gray-100">
                    <!-- <th class="px-4 py-2 border">Reference</th> -->
                    <th class="px-4 py-2 border">First Name</th>
                    <th class="px-4 py-2 border">Last Name</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Mobile</th>
                    <th class="px-4 py-2 border">Phone</th>
                    <th class="px-4 py-2 border">Banks</th>
                    <th class="px-4 py-2 border">Subscription Status</th>
                    <!-- <th class="px-4 py-2 border">Labels</th> -->
                    <th class="px-4 py-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agents as $agent)
                    <tr>
                        <!-- <td class="px-4 py-2 border">{{ $agent->reference }}</td> -->
                        <td class="px-4 py-2 border">{{ $agent->first_name }}</td>
                        <td class="px-4 py-2 border">{{ $agent->last_name }}</td>
                        <td class="px-4 py-2 border">{{ $agent->email }}</td>
                        <td class="px-4 py-2 border">{{ $agent->mobile }}</td>
                        <td class="px-4 py-2 border">{{ $agent->phone }}</td>
                        <td class="px-4 py-2 border">{{ $agent->phone }}</td>
                        <td class="px-4 py-2 border">{{ $agent->subscription_status }}</td>
                        <!-- <td class="px-4 py-2 border">
                            @if($agent->labels)
                                @foreach(json_decode($agent->labels, true) as $label)
                                    <span class="badge bg-primary">{{ $label }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No labels</span>
                            @endif
                        </td> -->
                        <td class="px-4 py-2 border">
                            <!-- View Button -->
                            <a href="{{ route('agents.show', $agent->id) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                            <!-- Edit Button -->
                            <a href="{{ route('agents.edit', $agent->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>
                            <!-- Delete Button -->
                            <form action="{{ route('agents.destroy', $agent->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this agent?')" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No agents found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $agents->links() }}
    </div>
</div>
@endsection
