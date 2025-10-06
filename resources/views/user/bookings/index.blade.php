@extends('layouts.app')
@section('title','My Bookings')

@section('styles')
<style>
  :root{
      --bg-foundation:#F4F6F8;
      --txt-main:#212529;
      --txt-secondary:#6C757D;
      --act-red:#E54D42;
      --accent-yellow:#FFB900;
  }
  body{ background:var(--bg-foundation); color:var(--txt-main); }
  .card-soft{ border:1px solid #E9ECEF; border-radius:.75rem; box-shadow:0 4px 14px rgba(33,37,41,.06); }
  .page-title{ color:var(--txt-main); }
  .text-secondary{ color:var(--txt-secondary)!important; }
  .btn-primary{ background:var(--act-red)!important; border-color:var(--act-red)!important; }
  .btn-primary:hover{ filter:brightness(0.95); }
  .btn-cancel{ background:var(--act-red)!important; border:none; color:#fff!important; border-radius:.5rem; padding:.4rem .9rem; font-size:.875rem; }
  .btn-cancel:hover{ filter:brightness(0.9); }
  /* สถานะ booking */
  .status{
    display:inline-block;
    padding:.3rem .75rem;
    border-radius:999px;
    font-size:.8rem;
    font-weight:600;
  }
  .status-pending{ background:#FFB900 !important; color:#212529 !important; }
  .status-approved{ background:#28a745 !important; color:#fff !important; }
  .status-cancelled{ background:#dc3545 !important; color:#fff !important; }
  .status-completed{ background:#0d6efd !important; color:#fff !important; }
</style>
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
