@extends('layouts.app')
@section('title','Edit Field')
@section('content')
<h1>Edit Field #{{ $field->id }}</h1>

<form method="POST" action="{{ route('admin.fields.update',$field) }}">
@csrf @method('PUT')
<div>Name <input name="name" value="{{ old('name',$field->name) }}" required></div>
<div>Sport Type <input name="sport_type" value="{{ old('sport_type',$field->sport_type) }}" required></div>
<div>Location <input name="location" value="{{ old('location',$field->location) }}" required></div>
<div>Capacity <input name="capacity" type="number" min="0" value="{{ old('capacity',$field->capacity) }}" required></div>
<div>
  Status
  <select name="status">
    @foreach(['available','closed','maintenance'] as $s)
      <option value="{{ $s }}" @selected(old('status',$field->status)===$s)>{{ $s }}</option>
    @endforeach
  </select>
</div>
<div>
  Owner (staff)
  <select name="owner_id">
    <option value="">-- none --</option>
    @foreach($staffs as $s)
      <option value="{{ $s->id }}" @selected(old('owner_id',$field->owner_id)==$s->id)>{{ $s->name }}</option>
    @endforeach
  </select>
</div>
<div>Min Duration (min) <input name="min_duration_minutes" type="number" min="15" value="{{ old('min_duration_minutes',$field->min_duration_minutes) }}"></div>
<div>Max Duration (min) <input name="max_duration_minutes" type="number" min="15" value="{{ old('max_duration_minutes',$field->max_duration_minutes) }}"></div>
<div>Lead Time (hours) <input name="lead_time_hours" type="number" min="0" value="{{ old('lead_time_hours',$field->lead_time_hours) }}"></div>
<p style="margin-top:12px;">
  Units in this field: <strong>{{ $field->units_count }}</strong>
  <a href="{{ route('admin.fields.units.index',$field) }}">Manage Units</a>
</p>
<button type="submit">Update</button>
</form>
@endsection
