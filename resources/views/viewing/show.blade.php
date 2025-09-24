@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Viewing Details</h1>
        <p><strong>Client Name:</strong> {{ $viewing->client_name }}</p>
        <p><strong>Assigned Agent:</strong> {{ $viewing->assignedTo->name }}</p>
        <p><strong>Property:</strong> {{ $viewing->property->name }}</p>
        <p><strong>Date & Time:</strong> {{ $viewing->viewing_date }} {{ $viewing->viewing_time }}</p>
        <p><strong>Client Email:</strong> {{ $viewing->client_email }}</p>
        <p><strong>Client Phone:</strong> {{ $viewing->client_phone }}</p>
        <p><strong>Client WhatsApp:</strong> {{ $viewing->client_whatsapp }}</p>

        <a href="{{ route('admin.viewings.index') }}" class="btn btn-primary">Back to Viewings</a>
    </div>
@endsection
