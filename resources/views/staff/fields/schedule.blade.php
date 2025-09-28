@extends('layouts.app')

@section('title','Field Schedule')

@section('content')
<h1>Field Schedule</h1>

@if($fields->isEmpty())
  <p>คุณยังไม่ได้รับมอบหมายสนาม</p>
@else
  <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <div>
      <label for="field-select">เลือกสนาม:</label>
      <select id="field-select">
        <option value="">-- เลือกสนาม --</option>
        @foreach($fields as $f)
          <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->sport_type }})</option>
        @endforeach
      </select>
    </div>
    <div>
      <label for="unit-select">เลือกคอร์ต:</label>
      <select id="unit-select" disabled>
        <option value="">-- เลือกคอร์ต --</option>
      </select>
    </div>
  </div>

  <div id="calendar" style="margin-top:16px;"></div>
@endif

<style>
  #calendar { max-width: 1100px; min-height: 600px; }
  .fc-booking-approved, .fc-booking-approved .fc-event-main { background:#4caf50!important; border-color:#4caf50!important; }
  .fc-booking-pending,  .fc-booking-pending  .fc-event-main { background:#ff9800!important; border-color:#ff9800!important; }
  .fc-closure { background: rgba(128,128,128,.35)!important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const { Calendar, dayGridPlugin, timeGridPlugin, interactionPlugin } = window.FC;

  const fieldSel = document.getElementById('field-select');
  const unitSel  = document.getElementById('unit-select');
  const calEl    = document.getElementById('calendar');

  // โหลด units ของสนามที่เลือก (ใช้ public endpoint ที่คุณมีอยู่แล้ว)
  async function loadUnits(fieldId) {
    unitSel.innerHTML = '<option value="">-- เลือกคอร์ต --</option>';
    unitSel.disabled = true;
    if (!fieldId) return;

    const res = await fetch(`/api/fields/${fieldId}/units`, { credentials: 'same-origin' });
    const units = await res.json();
    units.forEach(u => {
      const opt = document.createElement('option');
      opt.value = u.id;
      opt.textContent = `${u.name} (${u.status})`;
      unitSel.appendChild(opt);
    });
    unitSel.disabled = false;
  }

  const calendar = new Calendar(calEl, {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    height: 650,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '23:00:00',
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
    events(fetchInfo, success, failure) {
      const fid = fieldSel.value;
      const uid = unitSel.value;
      if (!fid || !uid) { success([]); return; }
      const url = `/api/fields/${fid}/units/${uid}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;
      fetch(url, { credentials:'same-origin' })
        .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
        .then(success)
        .catch(failure);
    },
    eventClassNames: arg => arg.event.extendedProps.className || arg.event.classNames || [],
  });

  calendar.render();

  fieldSel?.addEventListener('change', async () => {
    await loadUnits(fieldSel.value);
    calendar.refetchEvents();
  });

  unitSel?.addEventListener('change', () => {
    calendar.refetchEvents();
  });
});
</script>
@endsection
