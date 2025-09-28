@extends('layouts.app')
@section('title','Create Booking')

@section('content')
<h1>Create Booking</h1>

@if(session('status')) <div class="p-2 bg-green-100">{{ session('status') }}</div> @endif
@if($errors->any())
  <div class="p-2 bg-red-100">
    <ul class="list-disc ml-5">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
  @csrf

  {{-- เลือกสนาม --}}
  <div class="mb-3">
    <label for="sports_field_id">Field</label>
    <select id="sports_field_id" name="sports_field_id" required>
      <option value="">-- Select Field --</option>
      @foreach($fields as $f)
        <option value="{{ $f->id }}" @selected(old('sports_field_id',$prefield)==$f->id)>
          {{ $f->name }} ({{ $f->sport_type }})
        </option>
      @endforeach
    </select>
    @error('sports_field_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  {{-- เลือกคอร์ต --}}
  <div class="mb-3">
    <label for="field_unit_id">Court</label>
    <select id="field_unit_id" name="field_unit_id" required>
      <option value="">-- Select Court --</option>
      @if($prefield)
        @foreach(($fields->firstWhere('id',$prefield)?->units ?? []) as $u)
          <option value="{{ $u->id }}" @selected(old('field_unit_id',$preunit)==$u->id)>
            {{ $u->name }} ({{ $u->status }})
          </option>
        @endforeach
      @endif
    </select>
    @error('field_unit_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  {{-- ปฏิทินย่อ --}}
  <div class="mb-3">
    <label>Availability</label>
    <div id="mini-calendar" style="max-width: 1000px; border:1px solid #ddd; padding:8px;"></div>
    <small>
      • สีเทาพื้น = ปิดสนาม/คอร์ต • สีเขียว = Approved • สีส้ม = Pending<br>
      • คลิก/ลากช่วงเวลาว่างเพื่อเติมเวลาในฟอร์มอัตโนมัติ
    </small>
  </div>

  <div class="grid" style="grid-template-columns: repeat(2,minmax(0,240px)); gap:12px;">
    <div>
      <label for="date">Date</label>
      <input id="date" type="date" name="date" value="{{ old('date') }}" required>
      @error('date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label for="start_time">Start</label>
      <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required>
      @error('start_time')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label for="end_time">End</label>
      <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required>
      @error('end_time')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label for="contact_phone">Contact Phone</label>
      <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone') }}">
      @error('contact_phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="mb-3" style="margin-top:12px;">
    <label for="purpose">Purpose (optional)</label>
    <textarea id="purpose" name="purpose" rows="3">{{ old('purpose') }}</textarea>
    @error('purpose')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <button type="submit">Submit Booking</button>
</form>

{{-- โหลด FullCalendar จาก CDN เพื่อให้ขึ้นแน่นอน --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<style>
  #mini-calendar { min-height: 420px; }
  .fc-booking-approved, .fc-booking-approved .fc-event-main { background: #4caf50 !important; border-color: #4caf50 !important; }
  .fc-booking-pending,  .fc-booking-pending  .fc-event-main { background: #ff9800 !important; border-color: #ff9800 !important; }
  .fc-closure { background: rgba(128,128,128,0.35) !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const fieldSel = document.getElementById('sports_field_id');
  const unitSel  = document.getElementById('field_unit_id');
  const dateInp  = document.getElementById('date');
  const startInp = document.getElementById('start_time');
  const endInp   = document.getElementById('end_time');

  // โหลด units เมื่อเลือกสนาม
  fieldSel.addEventListener('change', () => {
    const fid = fieldSel.value;
    unitSel.innerHTML = '<option value="">-- Select Court --</option>';
    if (!fid) { calendar.refetchEvents(); return; }

    fetch(`/api/fields/${fid}/units`)
      .then(r => r.json())
      .then(units => {
        units.forEach(u => {
          const opt = document.createElement('option');
          opt.value = u.id;
          opt.textContent = `${u.name} (${u.status})`;
          unitSel.appendChild(opt);
        });
        calendar.refetchEvents();
      });
  });

  // FullCalendar
  const calEl = document.getElementById('mini-calendar');
  const calendar = new FullCalendar.Calendar(calEl, {
    initialView: 'timeGridWeek',
    height: 420,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '23:00:00',
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
    events: (fetchInfo, success, failure) => {
      const fid = fieldSel.value, uid = unitSel.value;
      if (!fid || !uid) { success([]); return; }
      const url = `/api/fields/${fid}/units/${uid}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.json()).then(success).catch(failure);
    },
    eventClassNames: arg => arg.event.extendedProps.className || arg.event.classNames || [],
    selectable: true,
    select: (info) => {
      // กรอกเวลาอัตโนมัติเมื่อผู้ใช้ลากเลือกช่องว่าง
      const pad = n => String(n).padStart(2,'0');
      const s = info.start, e = info.end;
      dateInp.value   = `${s.getFullYear()}-${pad(s.getMonth()+1)}-${pad(s.getDate())}`;
      startInp.value  = `${pad(s.getHours())}:${pad(s.getMinutes())}`;
      endInp.value    = `${pad(e.getHours())}:${pad(e.getMinutes())}`;
    },
    selectOverlap: () => false
  });
  calendar.render();

  // เปลี่ยนคอร์ต → โหลดอีเวนต์ใหม่
  unitSel.addEventListener('change', () => calendar.refetchEvents());

  // มี preselect field/unit มาแล้วจาก query → refetch ทันที
  if (fieldSel.value && unitSel.value) calendar.refetchEvents();

  // เปลี่ยน date → โกทูวันนั้น (ให้ผู้ใช้เห็นช่วงที่เลือก)
  dateInp.addEventListener('change', () => {
    if (dateInp.value) calendar.gotoDate(dateInp.value);
  });
});
</script>
@endsection
