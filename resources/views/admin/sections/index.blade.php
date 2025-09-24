@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editable Frontend Content</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Section</th>
                <th>Slug</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sections as $section)
                <tr>
                    <td>{{ ucwords(str_replace('-', ' ', $section->slug)) }}</td>
                    <td>{{ $section->slug }}</td>
                    <td>
                        <a href="{{ route('admin.sections.edit', $section->slug) }}" class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
