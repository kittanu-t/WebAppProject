@extends('layouts.app')

@section('title','Create Booking')

@section('content')
<h1>Create Booking</h1>

<form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
    @csrf

    <div class="mb-3">
        <label for="sports_field_id">Field</label>
        <select id="sports_field_id" name="sports_field_id" required>
            <option value="">-- Select Field --</option>
            @foreach($fields as $f)
                <option value="{{ $f->id }}"
                    @selected(old('sports_field_id', $prefield) == $f->id)>
                    {{ $f->name }} ({{ $f->sport_type }})
                </option>
            @endforeach
        </select>
        @error('sports_field_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    {{-- ปฏิทินย่อ แสดงเวลาที่ถูกจอง/ช่วงปิดสนามของสนามที่เลือก --}}
    <div class="mb-3">
        <label>Availability Calendar</label>
        <div id="mini-calendar" style="max-width: 800px; border:1px solid #ddd; padding:8px;"></div>
        <small>
            - สีพื้นเทา = ช่วงปิดสนาม (maintenance/closed)<br>
            - ช่องสี (Approved/Pending) = มีการจองแล้ว
        </small>
    </div>

    <div class="mb-3">
        <label for="date">Date</label>
        <input id="date" type="date" name="date" value="{{ old('date') }}" required>
        @error('date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="start_time">Start Time</label>
        <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required>
        @error('start_time')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="end_time">End Time</label>
        <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required>
        @error('end_time')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="purpose">Purpose</label>
        <textarea id="purpose" name="purpose" rows="3">{{ old('purpose') }}</textarea>
        @error('purpose')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="contact_phone">Contact Phone</label>
        <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone') }}">
        @error('contact_phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <button type="submit">Submit Booking</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fieldSelect = document.getElementById('sports_field_id');
    const dateInput   = document.getElementById('date');

    // กำหนดวันเริ่มมองเห็นในปฏิทิน (ถ้า user เลือกวันไว้แล้ว ให้ focus ไปวันนั้น)
    const initialDate = dateInput.value ? dateInput.value : undefined;

    const calendarEl = document.getElementById('mini-calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',      // มุมมองแบบสัปดาห์เพื่อเห็นช่วงเวลา
        height: 400,
        nowIndicator: true,
        slotMinTime: '06:00:00',
        slotMaxTime: '23:00:00',
        allDaySlot: false,
        navLinks: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialDate: initialDate,         // ถ้ามีค่า date ในฟอร์ม ให้เริ่มที่วันนั้น
        // แสดงสีตาม className ที่ฝั่ง server ส่งมา
        eventClassNames: function(arg) {
            return arg.event.extendedProps.className || [];
        },
        // โหลด events ตามสนามที่เลือก
        events: function(fetchInfo, successCallback, failureCallback) {
            const fieldId = fieldSelect.value;
            if (!fieldId) { successCallback([]); return; }

            const url = `/api/fields/${fieldId}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;

            fetch(url, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => successCallback(data))
                .catch(err => failureCallback(err));
        },
        eventDidMount: function(info) {
            // เพิ่ม title tooltip ง่าย ๆ
            if (info.event.title) {
                info.el.setAttribute('title', info.event.title);
            }
        },
        // ช่วยผู้ใช้: คลิกช่วงเวลาว่างแล้ว set ค่าในฟอร์ม (optional)
        selectable: true,
        select: function(selectionInfo) {
            const d = selectionInfo.start; // Date object (เริ่ม)
            // ตั้งค่าฟอร์ม date/start_time/end_time ให้ผู้ใช้
            const pad = (n)=> String(n).padStart(2,'0');
            const y = d.getFullYear(), m = pad(d.getMonth()+1), da = pad(d.getDate());
            const hh = pad(d.getHours()), mm = pad(d.getMinutes());

            dateInput.value = `${y}-${m}-${da}`;
            document.getElementById('start_time').value = `${hh}:${mm}`;

            // คำนวณ end time = start + 60 นาที (ค่าเริ่มต้น)
            const end = new Date(selectionInfo.start.getTime() + 60*60000);
            const eh = pad(end.getHours()), em = pad(end.getMinutes());
            document.getElementById('end_time').value = `${eh}:${em}`;
        },
        selectOverlap: function(event) {
            // ไม่อนุญาตเลือกทับ event/closure
            return false;
        }
    });
    calendar.render();

    // เมื่อเปลี่ยนสนาม → reload events
    fieldSelect.addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // เมื่อ user เลือกวันใน input date → ให้ calendar ไปยังวันนั้น (อำนวยความสะดวก)
    dateInput.addEventListener('change', function () {
        if (dateInput.value) calendar.gotoDate(dateInput.value);
    });

    // ถ้ามี preselect field จาก query string ก็โหลด event เลย
    if (fieldSelect.value) {
        calendar.refetchEvents();
        // ถ้ามีวันที่แล้ว focus วันนั้น
        if (initialDate) calendar.gotoDate(initialDate);
    }
});
</script>

@vite('resources/js/booking-create.js')

{{-- สีพื้นฐาน (หยาบ ๆ ไว้ก่อน) --}}
<style>
/* จากฝั่ง controller เราส่ง className เช่น fc-booking-approved / fc-booking-pending / fc-closure */
.fc-booking-approved, .fc-booking-approved .fc-event-main { background: #4caf50 !important; border-color: #4caf50 !important; }
.fc-booking-pending,  .fc-booking-pending  .fc-event-main { background: #ff9800 !important; border-color: #ff9800 !important; }
.fc-closure                                              { background: rgba(128,128,128,0.35) !important; }
</style>

@endsection
