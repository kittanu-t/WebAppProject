@extends('layouts.app')
@section('title','Edit Unit - '.$field->name)

@section('content')
<h1>Edit Unit — {{ $field->name }}</h1>
<form method="POST" action="{{ route('admin.fields.units.update', [$field,$unit]) }}">
  @csrf @method('PUT')
  <div>Name <input name="name" value="{{ old('name',$unit->name) }}" required></div>
  <div>Index <input type="number" name="index" value="{{ old('index',$unit->index) }}" min="1" required></div>
  <div>Capacity <input type="number" name="capacity" value="{{ old('capacity',$unit->capacity) }}" min="0" required></div>
  <div>
    Status
    <select name="status">
      @foreach(['available','closed','maintenance'] as $s)
        <option value="{{ $s }}" @selected(old('status',$unit->status)===$s)>{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <button type="submit">Update</button>
</form>
@endsection
