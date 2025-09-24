@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4>Edit Activity</h4>
    <form method="POST" action="{{ route('diary.update', $viewing->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $viewing->title) }}" required>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::parse($viewing->viewing_date)->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($viewing->viewing_date)->format('H:i') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Participant</label>
            <select name="assigned_to" class="form-select" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $viewing->assigned_to == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <label class="form-label">Activity Type</label>
            <select name="activity_type" class="form-select" required>
                <option value="Viewing" {{ $viewing->activity_type == 'Viewing' ? 'selected' : '' }}>Viewing</option>
                <option value="Take On" {{ $viewing->activity_type == 'Take On' ? 'selected' : '' }}>Take On</option>
                <option value="Misc" {{ $viewing->activity_type == 'Misc' ? 'selected' : '' }}>Misc</option>
            </select>
        </div>

        <div class="mt-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $viewing->notes) }}</textarea>
        </div>

        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="mark_done" id="mark_done" {{ $viewing->is_done ? 'checked' : '' }}>
            <label class="form-check-label" for="mark_done">Mark as done</label>
        </div>

        <div class="mt-4">
            <a href="{{ route('diary.index') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
