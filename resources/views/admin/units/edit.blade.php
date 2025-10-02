@extends('layouts.app')
@section('title', 'Edit Unit - '.$field->name)
@section('content')
<h1>Edit Unit — {{ $field->name }}</h1>
<form method="POST" action="{{ route('admin.fields.units.update', [$field, $unit]) }}" class="card p-4 shadow-sm rounded-4" style="border: 1px solid #6C757D;">
  @csrf @method('PUT')
  <div class="mb-3"><label>Name</label> <input name="name" value="{{ old('name', $unit->name) }}" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Index</label> <input type="number" name="index" value="{{ old('index', $unit->index) }}" min="1" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Capacity</label> <input type="number" name="capacity" value="{{ old('capacity', $unit->capacity) }}" min="0" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;">
      @foreach(['available', 'closed', 'maintenance'] as $s)
        <option value="{{ $s }}" @selected(old('status', $unit->status) === $s)>{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <button type="submit" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 6px 12px;">Update</button>
</form>
@endsection