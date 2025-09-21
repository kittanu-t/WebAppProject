@extends('layouts.app')

@section('title','Pending Bookings')

@section('content')
<h1>Pending Bookings</h1>

@if(session('status'))
    <div>{{ session('status') }}</div>
@endif

@forelse($bookings as $b)
  <div style="border:1px solid #ddd; padding:12px; margin-bottom:12px;">
      <div><strong>Booking #{{ $b->id }}</strong></div>
      <div>Field: {{ $b->sportsField->name ?? '-' }}</div>
      <div>User: {{ $b->user->name ?? '-' }} ({{ $b->user->email ?? '-' }})</div>
      <div>Date: {{ $b->date }} {{ $b->start_time }} - {{ $b->end_time }}</div>
      <div>Status: <strong>{{ ucfirst($b->status) }}</strong></div>

      {{-- ปุ่มชัด ๆ ไม่มี CSS --}}
      <div style="margin-top:8px;">
          <form method="POST" action="{{ route('staff.bookings.approve', $b->id) }}" style="display:inline;">
              @csrf
              <button type="submit">Approve</button>
          </form>
          <form method="POST" action="{{ route('staff.bookings.reject', $b->id) }}" style="display:inline; margin-left:8px;">
              @csrf
              <button type="submit">Reject</button>
          </form>
      </div>
  </div>
@empty
  <p>ไม่มี booking ที่รออนุมัติ</p>
@endforelse

{{ $bookings->links() }}
@endsection
