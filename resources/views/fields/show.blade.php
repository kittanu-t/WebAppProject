{{-- resources/views/fields/show.blade.php --}}
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

<style>
  /* 🎨 พื้นหลังขาว */
  body {
    background: #ffffff;
    font-family: Arial, sans-serif;
    color: black; /* ฟ้อนสีดำ */
  }

  #calendar { 
    max-width: 1100px; 
    min-height: 600px; 
    margin-top: 20px;
    background: #fff; 
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    padding: 10px;
  }

  /* ✅ Approved bookings = เขียว */
  .fc-booking-approved,
  .fc-booking-approved .fc-event-main {
    background:#4caf50 !important;
    border-color:#4caf50 !important;
    color: white !important;
    font-weight: bold;
  }

  /* ⏳ Pending bookings = เหลือง */
  .fc-booking-pending,
  .fc-booking-pending .fc-event-main {
    background:#ffeb3b !important;
    border-color:#fdd835 !important;
    color: black !important;
    font-weight: bold;
  }

  /* 🚫 Closure = เทาโปร่ง */
  .fc-closure {
    background: rgba(128,128,128,.35) !important;
    border-color: rgba(128,128,128,.35) !important;
    color: black !important;
  }

  /* ปุ่ม Header (month/week/day) */
  .fc .fc-button {
    background: #fbc02d;
    color: black;
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    font-weight: bold;
  }
  .fc .fc-button-active {
    background: #e53935 !important;
    color: white !important;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const { Calendar, dayGridPlugin, timeGridPlugin, interactionPlugin } = window.FC;

  const unitSelect = document.getElementById('unit-select');
  const calEl      = document.getElementById('calendar');
  const baseEvents = "{{ url('/api/fields/'.$field->id.'/units') }}";

  const cal = new Calendar(calEl, {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    height: 650,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '23:00:00',
    headerToolbar: { 
      left:'prev,next today', 
      center:'title', 
      right:'dayGridMonth,timeGridWeek,timeGridDay' 
    },
    events(fetchInfo, success, failure) {
      const unitId = unitSelect.value;
      if (!unitId) { success([]); return; }
      const url = `${baseEvents}/${unitId}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;
      fetch(url, { credentials:'same-origin' })
        .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
        .then(success)
        .catch(failure);
    },
    eventClassNames: arg => arg.event.extendedProps.className || arg.event.classNames || [],
    eventDidMount(info){ if (info.event.title) info.el.title = info.event.title; },
  });

  cal.render();
  unitSelect.addEventListener('change', () => cal.refetchEvents());
  if (unitSelect.value) cal.refetchEvents();
});
</script>
@endsection
