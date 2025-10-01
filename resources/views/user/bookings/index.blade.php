@extends('layouts.app')
@section('title','My Bookings')

@section('content')
<style>
  .booking-header {
    background-color: #FFD700;
    color: black;
    font-weight: bold;
    padding: 15px 25px;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .status {
    font-weight: bold;
    padding: 4px 10px;
    border-radius: 4px;
    display: inline-block;
  }

  .status-pending {
    background-color: #facc15; 
    color: black;
  }

  .status-approved {
    background-color: #3b82f6; 
    color: white;
  }

  .status-cancelled {
    background-color: #ef4444; 
    color: white;
  }

  .status-completed {
    background-color: #22c55e; 
    color: white;
  }
</style>
<h1 class="booking-header">My Bookings</h1>

@forelse($bookings as $b)
  <div class="border p-3 mb-3">
      <div><strong>#{{ $b->id }}</strong> — {{ $b->sportsField->name ?? '-' }}</div>
      <div>
        {{ $b->date }} ({{ $b->start_time }} - {{ $b->end_time }}) — 
        
        @php
          $statusClass = match($b->status) {
              'pending' => 'status status-pending',
              'approved' => 'status status-approved',
              'cancelled' => 'status status-cancelled',
              'completed' => 'status status-completed',
              default => 'status'
          };
        @endphp

        <span class="{{ $statusClass }}">{{ ucfirst($b->status) }}</span>
      </div>

      @if(!in_array($b->status, ['approved','completed','cancelled']))
        <form method="POST" action="{{ route('bookings.destroy', $b->id) }}" style="display:inline;">
            @csrf
            @method('DELETE') {{-- สำคัญ! ใช้ DELETE --}}
            <button type="submit">Cancel</button>
        </form>
      @endif
  </div>
@empty
  <p>ยังไม่มีการจอง</p>
@endforelse

@if(isset($bookings)) {{ $bookings->links() }} @endif
@endsection
