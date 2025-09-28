{{-- resources/views/fields/show.blade.php หรือ resources/views/user/fields/show.blade.php --}}
@extends('layouts.app')
@section('title', $field->name)

@section('content')
<h1>{{ $field->name }} ({{ $field->sport_type }})</h1>
<p>Location: {{ $field->location }} | Status: {{ $field->status }}</p>

<label for="unit-select">เลือกคอร์ต</label>
<select id="unit-select">
  @foreach($field->units->sortBy('index') as $u)
    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->status }})</option>
  @endforeach
</select>

<div id="calendar" style="margin-top:12px;"></div>

{{-- สไตล์พื้นฐานให้ container มีความสูง/มองเห็นแน่ ๆ --}}
<style>
  #calendar { max-width: 1100px; min-height: 600px; }
  .fc-booking-approved, .fc-booking-approved .fc-event-main { background:#4caf50!important; border-color:#4caf50!important; }
  .fc-booking-pending,  .fc-booking-pending  .fc-event-main { background:#ff9800!important; border-color:#ff9800!important; }
  .fc-closure { background: rgba(128,128,128,.35)!important; }
</style>

{{-- โหลด FullCalendar จาก CDN (กันพลาด asset) --}}
<!-- <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script> -->

<script>
document.addEventListener('DOMContentLoaded', () => {
  const unitSelect = document.getElementById('unit-select');
  const calEl      = document.getElementById('calendar');

  // base URL สำหรับ events ของ unit
  const baseUnitEvents = "{{ url('/api/fields/'.$field->id.'/units') }}";

  const calendar = new FullCalendar.Calendar(calEl, {
    initialView: 'timeGridWeek',
    height: 650,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '23:00:00',
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },

    // โหลด events ตาม unit ที่เลือก
    events: (fetchInfo, success, failure) => {
      const unitId = unitSelect.value;
      if (!unitId) { success([]); return; }
      const url = `${baseUnitEvents}/${unitId}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;
      fetch(url, { credentials:'same-origin' })
        .then(r => {
          if (!r.ok) throw new Error('HTTP '+r.status);
          return r.json();
        })
        .then(data => success(data))
        .catch(err => failure(err));
    },

    eventClassNames: arg => arg.event.extendedProps.className || arg.event.classNames || [],
    eventDidMount: info => { if (info.event.title) info.el.title = info.event.title; },
  });

  calendar.render();

  // เปลี่ยนคอร์ต → reload events
  unitSelect.addEventListener('change', () => calendar.refetchEvents());

  // preselect unit แรกแล้วโหลดทันที
  if (unitSelect.value) calendar.refetchEvents();
});
</script>
@endsection
