@extends('layouts.app')

@section('title','Field Schedule')

@section('content')
<h1>Field Schedule</h1>

@if($fields->isEmpty())
  <p>คุณยังไม่ได้รับมอบหมายสนาม</p>
@else
  <label for="field-select">เลือกสนาม:</label>
  <select id="field-select">
    @foreach($fields as $f)
      <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->sport_type }})</option>
    @endforeach
  </select>

  <div id="calendar" style="margin-top:16px;"></div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('field-select');
    const calEl = document.getElementById('calendar');

    if (!select) return;

    const calendar = new FullCalendar.Calendar(calEl, {
        initialView: 'timeGridWeek',
        height: 600,
        nowIndicator: true,
        slotMinTime: '06:00:00',
        slotMaxTime: '23:00:00',
        allDaySlot: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, success, failure) {
            const fieldId = select.value;
            if (!fieldId) { success([]); return; }
            fetch(`/staff/api/fields/${fieldId}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`)
                .then(r => r.json())
                .then(data => success(data))
                .catch(err => failure(err));
        }
    });

    calendar.render();

    select.addEventListener('change', function () {
        calendar.refetchEvents();
    });
});
</script>
@endsection
