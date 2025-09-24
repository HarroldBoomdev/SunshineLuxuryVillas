@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Edit Developer</h1>
    <form action="{{ route('developers.update', $developer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Reference -->
        <div class="mb-4">
            <label for="reference" class="block text-sm font-medium text-gray-700">Reference</label>
            <input type="text" name="reference" id="reference" value="{{ $developer->reference }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ $developer->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ $developer->email }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ $developer->phone }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Website -->
        <div class="mb-4">
            <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
            <input type="text" name="website" id="website" value="{{ $developer->website }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Developer</button>
        </div>
    </form>
</div>
@endsection
