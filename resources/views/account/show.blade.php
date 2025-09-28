@extends('layouts.app')
@section('title','My Account')

@section('content')
<h1>My Account</h1>

@if(session('success'))
  <div style="color: green;">{{ session('success') }}</div>
@endif

<h3>Basic Info</h3>
<form method="POST" action="{{ route('account.update') }}">
  @csrf @method('PUT')
  <div>Name: <input type="text" name="name" value="{{ old('name',$user->name) }}"></div>
  <div>Email: <input type="email" name="email" value="{{ old('email',$user->email) }}"></div>
  <div>Phone: <input type="text" name="phone" value="{{ old('phone',$user->phone) }}"></div>
  <button type="submit">Update Info</button>
</form>

<hr>

<h3>Change Password</h3>
<form method="POST" action="{{ route('account.password') }}">
  @csrf @method('PUT')
  <div>New Password: <input type="password" name="password"></div>
  <div>Confirm: <input type="password" name="password_confirmation"></div>
  <button type="submit">Change Password</button>
</form>
@endsection
