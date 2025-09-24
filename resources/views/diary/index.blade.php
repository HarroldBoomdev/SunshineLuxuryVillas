@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Then Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        min-height: 600px;
        border: 1px solid #ccc;
    }

    .table-list th, .table-list td {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .status-icon {
        width: 10px;
        height: 10px;
        background: red;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd;
        color: white;
        border: 1px solid #0a58ca;
        padding: 0 5px;
        margin-top: 5px;
    }

</style>

<div class="container py-4">
    <div class="mb-4">
        <button id="toggle-table-view" class="btn btn-primary">Table View</button>
        <button id="toggle-calendar-view" class="btn btn-secondary">Calendar View</button>
    </div>

    <div id="table-view">
    <h4 class="mb-3">Activities</h4>
    <table class="table table-bordered table-hover table-list">
        <thead class="table-light">
            <tr>
                <th class="px-4 py-2 border">Title</th>
                <th class="px-4 py-2 border">Clients</th>
                <th class="px-4 py-2 border">Emails</th>
                <th class="px-4 py-2 border">Phones</th>
                <th class="px-4 py-2 border">Properties</th>
                <th class="px-4 py-2 border">Due Date</th>
                <th class="px-4 py-2 border">Duration</th>
                <th class="px-4 py-2 border">Assigned To</th>
                <th class="px-4 py-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($viewings as $viewing)
                <tr>
                    <td class="px-4 py-2 border">{{ $viewing->title ?? 'N/A' }}</td>

                    @php
                        $clientIds = is_array($viewing->client_ids) ? $viewing->client_ids : json_decode($viewing->client_ids, true) ?? [];
                        $propertyIds = is_array($viewing->property_ids) ? $viewing->property_ids : json_decode($viewing->property_ids, true) ?? [];
                        $clientList = count($clientIds) ? \App\Models\ClientModel::whereIn('id', $clientIds)->get() : collect();
                        $propertyList = count($propertyIds) ? \App\Models\PropertiesModel::whereIn('id', $propertyIds)->get() : collect();
                    @endphp

                    <td class="px-4 py-2 border">
                        @forelse($clientList as $client)
                            {{ $client->first_name }} {{ $client->last_name }}<br>
                        @empty
                            N/A
                        @endforelse
                    </td>

                    <td class="px-4 py-2 border">
                        @forelse($clientList as $client)
                            {{ $client->email ?? 'N/A' }}<br>
                        @empty
                            N/A
                        @endforelse
                    </td>

                    <td class="px-4 py-2 border">
                        @forelse($clientList as $client)
                            {{ $client->phone ?? $client->mobile ?? '—' }}<br>
                        @empty
                            —
                        @endforelse
                    </td>

                    <td class="px-4 py-2 border">
                        @forelse($propertyList as $property)
                            {{ $property->reference ?? '' }} – {{ $property->title ?? '' }}<br>
                        @empty
                            N/A
                        @endforelse
                    </td>

                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($viewing->viewing_date)->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-2 border">{{ $viewing->duration ?? '—' }}</td>
                    <td class="px-4 py-2 border">{{ $viewing->assignedTo->name ?? '—' }}</td>

                    <td class="px-4 py-2 border">
                        {{-- Client Actions --}}
                        @foreach($clientList as $client)
                            <div class="mb-1 d-inline-block">
                                @can('client.contact.email')
                                    @if($client->email)
                                        <a href="mailto:{{ $client->email }}" class="btn btn-sm btn-primary" title="Email">
                                            <i class="fa fa-envelope"></i>
                                        </a>
                                    @endif
                                @endcan

                                @can('client.contact.whatsapp')
                                    @php $whatsappNumber = preg_replace('/[^0-9]/', '', $client->mobile ?? ''); @endphp
                                    @if($whatsappNumber)
                                        <a href="https://wa.me/{{ $whatsappNumber }}" class="btn btn-sm btn-success" title="WhatsApp">
                                            <i class="fa fa-whatsapp"></i>
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        @endforeach

                        {{-- Always show Edit --}}
                        @can('view_diary')
                            <a href="{{ route('diary.edit', $viewing->id) }}" class="btn btn-sm btn-warning" title="Edit Activity">
                                <i class="fa fa-edit"></i>
                            </a>
                        @endcan

                        {{-- Always show Delete --}}
                        @can('delete_diary')
                            <form action="{{ route('diary.destroy', $viewing->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this diary activity only?')"
                                        title="Delete Diary Entry">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No activities found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    </div>


    <div id="calendar" class="d-none mt-5"></div>
</div>

@include('diary.modal')

<script>
    window.diaryEvents = @json($events ?? []);
</script>

<script type='importmap'>
{
  "imports": {
    "@fullcalendar/core": "https://cdn.skypack.dev/@fullcalendar/core@6.1.7",
    "@fullcalendar/daygrid": "https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.7",
    "@fullcalendar/timegrid": "https://cdn.skypack.dev/@fullcalendar/timegrid@6.1.7",
    "@fullcalendar/interaction": "https://cdn.skypack.dev/@fullcalendar/interaction@6.1.7"
  }
}
</script>

<script type="module">
  import { Calendar } from '@fullcalendar/core'
  import dayGridPlugin from '@fullcalendar/daygrid'
  import timeGridPlugin from '@fullcalendar/timegrid'
  import interactionPlugin from '@fullcalendar/interaction'

  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new Calendar(calendarEl, {
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      height: 600,
      events: window.diaryEvents || [],
      dateClick: function(info) {
        const modal = new bootstrap.Modal(document.getElementById('createEventModal'));
        document.getElementById('eventDate').value = info.dateStr;
        modal.show();
      }
    });
    calendar.render();
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tableBtn = document.getElementById('toggle-table-view');
    const calendarBtn = document.getElementById('toggle-calendar-view');
    const tableView = document.getElementById('table-view');
    const calendarView = document.getElementById('calendar');

    tableBtn.addEventListener('click', () => {
      tableView.classList.remove('d-none');
      calendarView.classList.add('d-none');
    });

    calendarBtn.addEventListener('click', () => {
      tableView.classList.add('d-none');
      calendarView.classList.remove('d-none');
    });
  });
</script>

<script>
function setActivityType(type) {
    document.getElementById('activity_type').value = type;
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function updateDuration() {
    const start = document.getElementById('start_time').value;
    const end = document.getElementById('end_time').value;
    const durationField = document.getElementById('duration');

    if (start && end) {
        const [startH, startM] = start.split(':').map(Number);
        const [endH, endM] = end.split(':').map(Number);

        const startMinutes = startH * 60 + startM;
        const endMinutes = endH * 60 + endM;

        const diff = endMinutes - startMinutes;
        durationField.value = diff > 0 ? `${diff} mins` : 'Invalid';
    }
}

['DOMContentLoaded', 'change'].forEach(event => {
    document.addEventListener(event, () => {
        document.getElementById('start_time')?.addEventListener('change', updateDuration);
        document.getElementById('end_time')?.addEventListener('change', updateDuration);
    });
});
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#client_name').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search client...',
        ajax: {
            url: '{{ route("clients.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data };
            }
        }
    });
});
</script>

@endsection
