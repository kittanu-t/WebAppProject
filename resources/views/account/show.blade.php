@extends('layouts.app')
@section('title','My Account')

@section('styles')
<style>
  .submit-button {
    background-color: yellow;
    padding: 10px 20px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
  }

  .submit-button:hover {
    background-color: green;
    color: white;
  }
</style>

@section('content')
<h1 style="background-color: rgba(255, 187, 0, 0.77); padding: 10px;">My Account</h1>

@if(session('success'))
  <div style="color: green;">{{ session('success') }}</div>
@endif

<h3 style="background-color: rgba(255, 0, 0, 0.77); color: white; padding: 5px;">Basic Info</h3>
<form method="POST" action="{{ route('account.update') }}">
  @csrf @method('PUT')
  <div>Name: <input type="text" name="name" value="{{ old('name',$user->name) }}"></div>
  <div>Email: <input type="email" name="email" value="{{ old('email',$user->email) }}"></div>
  <div>Phone: <input type="text" name="phone" value="{{ old('phone',$user->phone) }}"></div>
  <button type="submit" class="submit-button">Update Info</button>
</form>

<hr>

<h3 style="background-color: rgba(255, 0, 0, 0.77); color: white; padding: 5px;">Change Password</h3>
<form method="POST" action="{{ route('account.password') }}">
  @csrf @method('PUT')
  <div>New Password: <input type="password" name="password"></div>
  <div>Confirm: <input type="password" name="password_confirmation"></div>
  <button type="submit" class="submit-button">Change Password</button>
</form>

@endsection

