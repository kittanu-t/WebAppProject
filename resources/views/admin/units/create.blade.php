@extends('layouts.app')
@section('title','Add Unit - '.$field->name)

@section('content')
<h1>Add Unit — {{ $field->name }}</h1>
<form method="POST" action="{{ route('admin.fields.units.store',$field) }}">
  @csrf
  <div>Name <input name="name" value="{{ old('name') }}" required></div>
  <div>Index <input type="number" name="index" value="{{ old('index',1) }}" min="1" required></div>
  <div>Capacity <input type="number" name="capacity" value="{{ old('capacity',1) }}" min="0" required></div>
  <div>
    Status
    <select name="status">
      @foreach(['available','closed','maintenance'] as $s)
        <option value="{{ $s }}" @selected(old('status')===$s)>{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <button type="submit">Save</button>
</form>
@endsection
