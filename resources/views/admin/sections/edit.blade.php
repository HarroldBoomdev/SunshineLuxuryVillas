@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Section: {{ $section->title }}</h2>

    <form method="POST" action="{{ route('sections.update', $section->slug) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @php
            $data = is_string($section->data) ? json_decode($section->data, true) : $section->data;
        @endphp

        @foreach ($data as $key => $value)
            <div class="form-group mb-3">
                <label for="{{ $key }}">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>

                @if (Str::contains($key, 'image'))
                    <input type="file" class="form-control" name="{{ $key }}_upload">
                    @if (!empty($value))
                        <small>Current: {{ $value }}</small>
                    @endif
                @else
                    <input type="text" class="form-control" name="{{ $key }}" value="{{ $value }}">
                @endif
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="{{ route('sections.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
