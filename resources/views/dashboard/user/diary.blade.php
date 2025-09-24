<!-- ✅ Debug Banner -->
<!-- Calendar Section -->
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Agent Selector -->
        <div class="mt-8 p-4 bg-white rounded-lg shadow-md" id="calendar-section">
            <label for="agent-select" class="block font-bold text-lg mb-2">Select Agent</label>
            <select id="agent-select" class="form-select w-full border-gray-300 rounded-md shadow-sm mb-4">
                <option value="">-- Select Agent --</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>

            <div id="calendar" style="min-height: 500px;"></div>
        </div>
    </div>
</div>

<!-- FullCalendar Styles and Scripts -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Calendar Logic -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const agentSelect = document.getElementById('agent-select');

    if (!calendarEl) {
        console.warn("⚠️ Calendar DOM element not found.");
        return;
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: [] // Start empty
    });

    calendar.render();

    agentSelect?.addEventListener('change', function () {
        const agentId = agentSelect.value;

        if (!agentId) {
            calendar.removeAllEvents();
            return;
        }

        fetch(`/api/diary-entries/${agentId}`)
            .then(response => response.json())
            .then(data => {
                const events = data.map(entry => ({
                    title: entry.title,
                    start: entry.start,
                    end: entry.end,
                    color: entry.color || '#008fd1'
                }));

                calendar.removeAllEvents();
                calendar.addEventSource(events);
            })
            .catch(error => {
                console.error('Error fetching events:', error);
            });
    });
});
</script>
