@extends('layouts.app')

@section('content')
<div class="container">
@include('layouts.newButton')
    <h1 class="text-xl font-bold mb-4">Add New Developer</h1>
    <form action="{{ route('developers.store') }}" method="POST">
        @csrf <!-- CSRF protection -->

        <!-- Reference -->
        <div class="mb-4">
            <label for="reference" class="block text-sm font-medium text-gray-700">Reference</label>
            <input type="text" name="reference" id="reference" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Website -->
        <div class="mb-4">
            <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
            <input type="text" name="website" id="website" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Developer</button>
        </div>
    </form>
</div>
@endsection
