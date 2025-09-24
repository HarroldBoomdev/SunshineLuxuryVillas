@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>View Diary Entry</h3>
    <div class="card p-4">
        <p><strong>Title:</strong> {{ $viewing->title }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($viewing->viewing_date)->format('M d, Y H:i') }}</p>
        <p><strong>Duration:</strong> {{ $viewing->duration }}</p>
        <p><strong>Type:</strong> {{ $viewing->activity_type }}</p>
        <p><strong>Notes:</strong> {{ $viewing->notes }}</p>
        <p><strong>Linked To:</strong> {{ $viewing->linked_to }}</p>
        <p><strong>Assigned To:</strong> {{ $viewing->assignedTo->name ?? '—' }}</p>

        <a href="{{ route('diary.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
