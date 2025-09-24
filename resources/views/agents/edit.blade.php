@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Edit Agent</h1>
    <form action="{{ route('agents.update', $agent->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Reference -->
        <div class="mb-3">
            <label for="reference" class="form-label">Reference</label>
            <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference', $agent->reference) }}" required>
        </div>

        <!-- First Name -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $agent->first_name) }}" required>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $agent->last_name) }}" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $agent->email) }}" required>
        </div>

        <!-- Mobile -->
        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile</label>
            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile', $agent->mobile) }}">
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $agent->phone) }}">
        </div>

        <!-- Website -->
        <div class="mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="text" class="form-control" id="website" name="website" value="{{ old('website', $agent->website) }}">
        </div>

        <!-- Subscription Status -->
        <div class="mb-3">
            <label for="subscription_status" class="form-label">Subscription Status</label>
            <input type="text" class="form-control" id="subscription_status" name="subscription_status" value="{{ old('subscription_status', $agent->subscription_status) }}">
        </div>

        <!-- Labels -->
        <div class="mb-3">
            <label for="labels" class="form-label">Labels</label>
            <input type="text" class="form-control" id="labels" name="labels" value="{{ old('labels', $agent->labels) }}" placeholder="Comma-separated labels (e.g., 'Seller,Premium')">
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Update Agent</button>
        <a href="{{ route('agents.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
