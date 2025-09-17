@extends('layouts.app')
@section('title','Field Schedule')

@section('content')
<h1>Staff: Field Schedule</h1>

{{-- เลือกสนามที่ดูแล (ภายหลังค่อยทำ dynamic); ตอนนี้สมมุติ field_id = 1 --}}
@php $fieldId = request('field_id', 1); @endphp

<div id="calendar"
     data-events-url="{{ route('staff.fields.events', $fieldId) }}">
</div>

{{-- Init FullCalendar --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('calendar')
    const eventsUrl = el.dataset.eventsUrl

    const { Calendar, dayGridPlugin, timeGridPlugin, interactionPlugin } = window.FullCalendar

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',    // หรือ 'dayGridMonth'
        height: 'auto',
        selectable: false,
        nowIndicator: true,
        // ถ้าคุณอยากกำหนด timezone: 'local' หรือ 'UTC' ก็ได้ (ค่อยตัดสินทีหลัง)
        // timeZone: 'local',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // ดึง events จาก endpoint เรา
        events: function(fetchInfo, successCallback, failureCallback) {
            const params = new URLSearchParams({
                start: fetchInfo.startStr,
                end:   fetchInfo.endStr
            })
            fetch(`${eventsUrl}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => successCallback(data))
            .catch(err => failureCallback(err))
        },

        // ป้องกัน drag/resize ชั่วคราว (เดี๋ยวค่อยเปิดถ้าต้องการปรับเวลาบน calendar)
        editable: false,
        eventResizableFromStart: false,

        // ปรับแต่ง render คร่าว ๆ (CSS ค่อยทำทีหลัง)
        eventDidMount: function(info) {
            // ถ้าต้องการ tooltip อย่างง่าย
            if (info.event.title) {
                info.el.setAttribute('title', info.event.title)
            }
        },
    })

    calendar.render()
})
</script>
@endsection
