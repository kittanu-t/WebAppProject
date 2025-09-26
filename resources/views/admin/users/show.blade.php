@extends('layouts.app')
@section('title','User Detail')
@section('content')
<h1>User #{{ $user->id }}</h1>
<p>Name: {{ $user->name }}</p>
<p>Email: {{ $user->email }}</p>
<p>Role: {{ $user->role }}</p>
<p>Active: {{ $user->active ? 'Yes' : 'No' }}</p>
@endsection
