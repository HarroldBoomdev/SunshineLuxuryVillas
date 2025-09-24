@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Schedule a Viewing</h1>
        <form action="{{ route('viewing.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="viewing_date">Viewing Date</label>
                <input type="date" name="viewing_date" id="viewing_date" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="viewing_time">Viewing Time</label>
                <input type="time" name="viewing_time" id="viewing_time" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="assigned_to">Assigned Agent</label>
                <select name="assigned_to" id="assigned_to" class="form-control" required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group mb-3">
                <label for="client_name">Client Name</label>
                <input type="text" name="client_name" id="client_name" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="client_email">Client Email</label>
                <input type="email" name="client_email" id="client_email" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="client_phone">Client Phone</label>
                <input type="text" name="client_phone" id="client_phone" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="client_whatsapp">Client WhatsApp</label>
                <input type="text" name="client_whatsapp" id="client_whatsapp" class="form-control">
            </div>

            <div class="form-group mb-3">
                <label for="property_id">Property</label>
                <select name="property_id" id="property_id" class="form-control" required>
                    @foreach ($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Schedule Viewing</button>
        </form>
    </div>
@endsection
