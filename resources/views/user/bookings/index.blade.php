@extends('layouts.app')

@section('title','Manage Bookings')

@section('content')
<h1>Bookings for My Fields</h1>

@if($bookings->count() === 0)
  <p>ยังไม่มีการจอง</p>
@endif

@foreach($bookings as $b)
  <div class="border p-3 mb-3">
    <div>
      <strong>Field:</strong> {{ $b->sportsField->name ?? '-' }} <br>
      <strong>User:</strong> {{ $b->user->name ?? '-' }} <br>
      <strong>Date:</strong> {{ $b->date }} ({{ $b->start_time }} - {{ $b->end_time }}) <br>
      <strong>Status:</strong> {{ $b->status }}
    </div>

    @if($b->status === 'pending')
      <form method="POST" action="{{ route('staff.bookings.approve', $b->id) }}" class="inline">
        @csrf
        <button class="bg-green-500 text-white px-2 py-1">Approve</button>
      </form>
      <form method="POST" action="{{ route('staff.bookings.reject', $b->id) }}" class="inline">
        @csrf
        <button class="bg-red-500 text-white px-2 py-1">Reject</button>
      </form>
    @endif
  </div>
@endforeach

<div>{{ $bookings->links() }}</div>
@endsection
