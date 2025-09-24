@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Edit User Role & Permissions</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-bold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>


                <div class="mb-3">
                    <label for="role" class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Permissions</label>
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-md-4 col-sm-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                           id="permission{{ $permission->id }}"
                                           name="permissions[]" value="{{ $permission->name }}"
                                           {{ $user->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.querySelector('select[name="role"]');
        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

        function togglePermissions() {
            if (roleSelect.value === 'Admin') {
                // Check all boxes only if Admin is selected
                permissionCheckboxes.forEach(cb => cb.checked = true);
            }
            // Do nothing (preserve existing checks) for other roles
        }

        // Only check all if the role is Admin on load
        if (roleSelect.value === 'Admin') {
            togglePermissions();
        }

        // Update checkboxes only when role changes to Admin
        roleSelect.addEventListener('change', function () {
            if (roleSelect.value === 'Admin') {
                togglePermissions();
            }
        });
    });
</script>


@endsection
