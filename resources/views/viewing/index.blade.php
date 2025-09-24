@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>All Viewings</h1>
        <a href="{{ route('admin.viewings.create') }}" class="btn btn-primary mb-3">Schedule a Viewing</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client Name</th>
                    <th>Assigned Agent</th>
                    <th>Property</th>
                    <th>Date & Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($viewings as $viewing)
                    <tr>
                        <td>{{ $viewing->id }}</td>
                        <td>{{ $viewing->client_name }}</td>
                        <td>{{ $viewing->assignedTo->name }}</td>
                        <td>{{ $viewing->property->name }}</td>
                        <td>{{ $viewing->viewing_date }} {{ $viewing->viewing_time }}</td>
                        <td>
                            <a href="{{ route('admin.viewings.show', $viewing->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('admin.viewings.edit', $viewing->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.viewings.destroy', $viewing->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
