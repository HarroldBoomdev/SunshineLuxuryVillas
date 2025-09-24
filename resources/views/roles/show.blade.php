@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">User Role & Permissions</h4>
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label fw-bold">User ID:</label>
                <div>{{ $user->id }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Name:</label>
                <div>{{ $user->name }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email:</label>
                <div>{{ $user->email }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Roles:</label>
                <div>
                    @forelse($user->roles as $role)
                        <span class="badge bg-info me-1">{{ $role->name }}</span>
                    @empty
                        <span class="text-muted">No roles assigned</span>
                    @endforelse
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Permissions:</label>
                <div>
                    @forelse($user->permissions as $permission)
                        <span class="badge bg-secondary me-1 mb-1">{{ $permission->name }}</span>
                    @empty
                        <span class="text-muted">No permissions assigned</span>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
