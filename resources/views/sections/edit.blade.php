@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Edit Section: {{ $section->slug }}</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sections.update', $section->slug) }}">
        @csrf
        @method('PUT')

        <label class="block font-medium mb-2">JSON Content</label>
        <textarea name="data" rows="15"
                  class="w-full p-3 border border-gray-300 rounded"
                  required>{{ json_encode(json_decode($section->data), JSON_PRETTY_PRINT) }}</textarea>

        @error('data')
            <div class="text-red-600 mt-1">{{ $message }}</div>
        @enderror

        <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Save Changes
        </button>
    </form>
</div>
@endsection
