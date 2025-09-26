@extends('layouts.app')
@section('title','Edit User')
@section('content')
<h1>Edit User #{{ $user->id }}</h1>

@if($errors->any()) <div>{{ $errors->first() }}</div> @endif

<form method="POST" action="{{ route('admin.users.update',$user) }}">
@csrf @method('PUT')
<div>Name <input name="name" value="{{ old('name',$user->name) }}" required></div>
<div>Email <input name="email" type="email" value="{{ old('email',$user->email) }}" required></div>
<div>Phone <input name="phone" value="{{ old('phone',$user->phone) }}"></div>
<div>
  Role
  <select name="role" required>
    @foreach(['admin','staff','user'] as $r)
      <option value="{{ $r }}" @selected(old('role',$user->role) === $r)>{{ ucfirst($r) }}</option>
    @endforeach
  </select>
</div>
<div>
  Active
  <select name="active">
    <option value="1" @selected(old('active',$user->active)=='1')>Yes</option>
    <option value="0" @selected(old('active',$user->active)=='0')>No</option>
  </select>
</div>
<div>Password (leave blank to keep) <input name="password" type="password"></div>
<div>Confirm <input name="password_confirmation" type="password"></div>

<button type="submit">Update</button>
</form>
@endsection
