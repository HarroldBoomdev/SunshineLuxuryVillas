<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">Diary Report</h2>
            <p class="text-sm text-gray-500">Monthly calendar + activity list (dummy data for now).</p>
        </div>

        <div class="flex items-center gap-2">
            <select id="diaryMonth" class="form-select form-select-sm">
                @foreach($months as $m)
                    <option value="{{ $m['value'] }}" @selected($m['value'] === $selectedMonth)>{{ $m['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card bg-blue-500 text-white">
            <p class="text-sm opacity-80">Today</p>
            <p class="text-3xl font-bold">{{ $kpis['today'] }}</p>
            <p class="text-xs opacity-80">activities</p>
        </div>

        <div class="card bg-green-500 text-white">
            <p class="text-sm opacity-80">This Week</p>
            <p class="text-3xl font-bold">{{ $kpis['this_week'] }}</p>
            <p class="text-xs opacity-80">scheduled</p>
        </div>

        <div class="card bg-yellow-500 text-white">
            <p class="text-sm opacity-80">Upcoming (7d)</p>
            <p class="text-3xl font-bold">{{ $kpis['upcoming_7'] }}</p>
            <p class="text-xs opacity-80">next 7 days</p>
        </div>

        <div class="card bg-red-500 text-white">
            <p class="text-sm opacity-80">Overdue</p>
            <p class="text-3xl font-bold">{{ $kpis['overdue'] }}</p>
            <p class="text-xs opacity-80">follow-ups</p>
        </div>
    </div>

    {{-- Calendar + List --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Calendar --}}
        <div class="card lg:col-span-1">
            <div class="flex items-center justify-between mb-3">
                <h3 class="card-title">Calendar</h3>
                <span class="text-xs text-gray-400">{{ $selectedMonthLabel }}</span>
            </div>

            <div class="grid grid-cols-7 gap-2 text-xs text-gray-500 mb-2">
                <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
            </div>

            <div class="grid grid-cols-7 gap-2">
                @foreach($calendarDays as $d)
                    @php
                        $isMuted = $d['inMonth'] ? '' : 'opacity-40';
                        $isToday = $d['isToday'] ? 'ring-2 ring-blue-500' : '';
                        $hasEvents = $d['count'] > 0;
                    @endphp

                    <button
                        type="button"
                        class="p-2 rounded border text-left hover:bg-gray-50 {{ $isMuted }} {{ $isToday }}"
                        data-date="{{ $d['date'] }}"
                        onclick="selectDiaryDate('{{ $d['date'] }}')"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">{{ $d['day'] }}</span>
                            @if($hasEvents)
                                <span class="badge bg-primary">{{ $d['count'] }}</span>
                            @endif
                        </div>
                        <div class="text-[11px] text-gray-500 mt-1">
                            {{ $d['short'] }}
                        </div>
                    </button>
                @endforeach
            </div>

            <hr class="my-4">

            <div class="text-xs text-gray-500">
                Tip: click a day to filter the list on the right.
            </div>
        </div>

        {{-- List --}}
        <div class="card lg:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h3 class="card-title">Activities</h3>
                <div class="text-xs text-gray-400" id="diaryListHint">
                    Showing: {{ $selectedMonthLabel }}
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Client</th>
                        <th>Property</th>
                        <th>Agent</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody id="diaryTableBody">
                    @foreach($activities as $a)
                        <tr data-date="{{ $a['date'] }}">
                            <td class="font-medium">{{ $a['date'] }}</td>
                            <td>{{ $a['time'] }}</td>
                            <td><span class="badge bg-light text-dark">{{ $a['type'] }}</span></td>
                            <td>{{ $a['client'] }}</td>
                            <td>{{ $a['property'] }}</td>
                            <td>{{ $a['agent'] }}</td>
                            <td>
                                @php
                                    $b = match($a['status']) {
                                        'Confirmed' => 'badge-success',
                                        'Pending'   => 'badge-warning',
                                        'Done'      => 'badge-secondary',
                                        'Cancelled' => 'badge-danger',
                                        default     => 'badge-light'
                                    };
                                @endphp
                                <span class="badge {{ $b }}">{{ $a['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-xs text-gray-500 mt-3">
                Total in view: <span id="diaryCount">{{ count($activities) }}</span>
            </div>
        </div>
    </div>

</div>

<script>
(function(){
    const monthSel = document.getElementById('diaryMonth');

    if (monthSel) {
        monthSel.addEventListener('change', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('month', monthSel.value);
            window.location.href = url.toString();
        });
    }

    window.selectDiaryDate = function(dateStr) {
        const rows = document.querySelectorAll('#diaryTableBody tr');
        let shown = 0;

        rows.forEach(r => {
            const d = r.getAttribute('data-date');
            const match = (d === dateStr);
            r.style.display = match ? '' : 'none';
            if (match) shown++;
        });

        document.getElementById('diaryListHint').textContent = 'Showing date: ' + dateStr;
        document.getElementById('diaryCount').textContent = shown;
    }
})();
</script>
