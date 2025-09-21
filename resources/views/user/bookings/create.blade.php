@extends('layouts.app')

@section('title','Create Booking')

@section('content')
<h1>Create Booking</h1>

<form method="POST" action="{{ route('bookings.store') }}">
    @csrf

    <div class="mb-3">
        <label for="sports_field_id">Field</label>
        <select id="sports_field_id" name="sports_field_id" required>
            <option value="">-- Select Field --</option>
            @foreach($fields as $f)
                <option value="{{ $f->id }}"
                    @selected(old('sports_field_id', $prefield) == $f->id)>
                    {{ $f->name }} ({{ $f->sport_type }})
                    — min {{ $f->min_duration_minutes }}m / max {{ $f->max_duration_minutes }}m — lead {{ $f->lead_time_hours }}h
                </option>
            @endforeach
        </select>
        @error('sports_field_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
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
@endsection
