@extends('layouts.app')
@section('title','Create User')
@section('content')
<h1>Create User</h1>

<form method="POST" action="{{ route('admin.users.store') }}">
@csrf
<div>Name <input name="name" value="{{ old('name') }}" required></div>
<div>Email <input name="email" type="email" value="{{ old('email') }}" required></div>
<div>Phone <input name="phone" value="{{ old('phone') }}"></div>
<div>
  Role
  <select name="role" required>
    @foreach(['admin','staff','user'] as $r)
      <option value="{{ $r }}" @selected(old('role') === $r)>{{ ucfirst($r) }}</option>
    @endforeach
  </select>
</div>
<div>
  Active
  <select name="active">
    <option value="1" @selected(old('active','1')=='1')>Yes</option>
    <option value="0" @selected(old('active')=='0')>No</option>
  </select>
</div>
<div>Password <input name="password" type="password" required></div>
<div>Confirm <input name="password_confirmation" type="password" required></div>

<button type="submit">Save</button>
</form>
@endsection
