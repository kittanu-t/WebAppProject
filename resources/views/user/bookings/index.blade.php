@extends('layouts.app')
@section('title','My Bookings')

@section('styles')
@endsection

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold page-title mb-0">My Bookings</h1>
    <a href="{{ route('bookings.create') }}" class="btn btn-primary px-3 py-2">+ New Booking</a>
  </div>

  {{-- รายการจอง --}}
  @forelse($bookings as $b)
    <div class="card card-soft mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
          <div>
            <strong class="text-dark">#{{ $b->id }}</strong> — 
            <span class="fw-medium">{{ $b->sportsField->name ?? '-' }}</span>
          </div>
          <div>
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
        </div>

        <div class="text-secondary small mb-3">
          {{ $b->date }} | {{ $b->start_time }} - {{ $b->end_time }}
        </div>

        @if(!in_array($b->status, ['approved','completed','cancelled']))
          <form method="POST" action="{{ route('bookings.destroy', $b->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-cancel">Cancel</button>
          </form>
        @endif
      </div>
    </div>
  @empty
    <div class="alert alert-light border rounded-4 shadow-sm text-center text-secondary">
      ยังไม่มีการจอง
    </div>
  @endforelse

  {{-- Pagination --}}
  @if(isset($bookings))
    <div class="mt-4">
      {{ $bookings->links() }}
    </div>
  @endif
</div>
@endsection
