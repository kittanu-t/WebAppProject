@extends('layouts.app')

@section('title', 'Field: ' . $field->name)

@section('content')
<h1>{{ $field->name }}</h1>
<p>Sport: {{ $field->sport_type }} | Location: {{ $field->location }} | Capacity: {{ $field->capacity }}</p>
<p>Status: {{ $field->status }}</p>

<div id="calendar" data-events-url="{{ route('fields.events', $field->id) }}"></div>

{{-- ปุ่มไปหน้าจอง (จะทำ logic ทีหลัง) --}}
<div class="mt-4">
    <a href="{{ route('bookings.create') }}?field_id={{ $field->id }}">ไปหน้าจองสนามนี้</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('calendar')
    const eventsUrl = el.dataset.eventsUrl
    const { Calendar, dayGridPlugin, timeGridPlugin, interactionPlugin } = window.FullCalendar

    const cal = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        height: 'auto',
        nowIndicator: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: (fetchInfo, ok, fail) => {
            const params = new URLSearchParams({
                start: fetchInfo.startStr,
                end:   fetchInfo.endStr
            })
            fetch(`${eventsUrl}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => ok(data))
            .catch(err => fail(err))
        },
        editable: false,
        eventDidMount: (info) => {
            if (info.event.title) info.el.title = info.event.title
        }
    })

    cal.render()
})
</script>
@endsection
