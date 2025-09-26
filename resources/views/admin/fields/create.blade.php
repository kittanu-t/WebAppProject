@extends('layouts.app')
@section('title','Create Field')
@section('content')
<h1>Create Field</h1>

<form method="POST" action="{{ route('admin.fields.store') }}">
@csrf
<div>Name <input name="name" value="{{ old('name') }}" required></div>
<div>Sport Type <input name="sport_type" value="{{ old('sport_type') }}" required></div>
<div>Location <input name="location" value="{{ old('location') }}" required></div>
<div>Capacity <input name="capacity" type="number" min="0" value="{{ old('capacity',0) }}" required></div>
<div>
  Status
  <select name="status">
    @foreach(['available','closed','maintenance'] as $s)
      <option value="{{ $s }}" @selected(old('status')===$s)>{{ $s }}</option>
    @endforeach
  </select>
</div>
<div>
  Owner (staff)
  <select name="owner_id">
    <option value="">-- none --</option>
    @foreach($staffs as $s)
      <option value="{{ $s->id }}" @selected(old('owner_id')==$s->id)>{{ $s->name }}</option>
    @endforeach
  </select>
</div>
<div>Min Duration (min) <input name="min_duration_minutes" type="number" min="15" value="{{ old('min_duration_minutes',60) }}"></div>
<div>Max Duration (min) <input name="max_duration_minutes" type="number" min="15" value="{{ old('max_duration_minutes',180) }}"></div>
<div>Lead Time (hours) <input name="lead_time_hours" type="number" min="0" value="{{ old('lead_time_hours',1) }}"></div>

<button type="submit">Save</button>
</form>
@endsection
