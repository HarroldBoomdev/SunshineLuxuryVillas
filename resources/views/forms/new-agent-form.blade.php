@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="font-weight-bold">Add New Agent</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('agents.store') }}" method="POST">
                @csrf

                <!-- Reference -->
                <div class="mb-3">
                    <label for="reference" class="form-label">Reference</label>
                    <input type="text" name="reference" id="reference" class="form-control" required>
                </div>

                <!-- Labels -->
                <div class="mb-3">
                    <label for="labels" class="form-label">Labels (Optional)</label>
                    <input type="text" name="labels" id="labels" class="form-control" placeholder="Comma-separated labels (e.g., Seller,Premium)">
                </div>

                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <!-- Mobile -->
                <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile</label>
                    <input type="text" name="mobile" id="mobile" class="form-control">
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control">
                </div>

                <!-- Website -->
                <div class="mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input type="text" name="website" id="website" class="form-control">
                </div>

                <!-- Subscription Status -->
                <div class="mb-3">
                    <label for="subscription_status" class="form-label">Subscription Status</label>
                    <input type="text" name="subscription_status" id="subscription_status" class="form-control">
                </div>

                <!-- Submit Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Save Agent</button>
                    <a href="{{ route('agents.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
