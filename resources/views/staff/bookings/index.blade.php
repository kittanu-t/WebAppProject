@extends('layouts.app')

@section('title','Pending Bookings')

@section('content')
<h1>Pending Bookings</h1>

@if(session('status'))
    <div class="alert-success">
        {{ session('status') }}
    </div>
@endif

@forelse($bookings as $b)
  <div class="booking-card">
      <div class="booking-header">
          <strong>Booking #{{ $b->id }}</strong>
      </div>
      <div><strong>Field:</strong> {{ $b->sportsField->name ?? '-' }}</div>
      <div><strong>User:</strong> {{ $b->user->name ?? '-' }} ({{ $b->user->email ?? '-' }})</div>
      <div><strong>Date:</strong> {{ $b->date }} {{ $b->start_time }} - {{ $b->end_time }}</div>
      <div>
        <strong>Status:</strong> 
        <span class="status-badge {{ $b->status }}">{{ ucfirst($b->status) }}</span>
      </div>

      {{-- ปุ่มชัด ๆ --}}
      <div class="action-buttons">
          <form method="POST" action="{{ route('staff.bookings.approve', $b->id) }}" style="display:inline;">
              @csrf
              <button type="submit" class="btn-approve">Approve</button>
          </form>
          <form method="POST" action="{{ route('staff.bookings.reject', $b->id) }}" style="display:inline;">
              @csrf
              <button type="submit" class="btn-reject">Reject</button>
          </form>
      </div>
  </div>
@empty
  <p>ไม่มี booking ที่รออนุมัติ</p>
@endforelse

{{ $bookings->links() }}

<style>
  body {
    background: #fff;
    color: black;
    font-family: Arial, sans-serif;
  }

  h1 {
    margin-bottom: 20px;
  }

  .alert-success {
    background: #e8f5e9;
    border: 1px solid #4caf50;
    color: #2e7d32;
    padding: 10px 14px;
    border-radius: 6px;
    margin-bottom: 16px;
  }

  .booking-card {
    background: #fff;
    border: 1px solid #eee;
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 8px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.05);
  }

  .booking-header {
    font-size: 16px;
    margin-bottom: 8px;
    color: #333;
  }

  /* Badge สถานะ */
  .status-badge {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: bold;
  }
  .status-badge.pending { background: #ffeb3b; color: black; }
  .status-badge.approved { background: #4caf50; color: white; }
  .status-badge.rejected { background: #e53935; color: white; }

  /* ปุ่ม */
  .action-buttons {
    margin-top: 12px;
  }

  .btn-approve, .btn-reject {
    padding: 6px 14px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: bold;
  }
  .btn-approve {
    background: #4caf50;
    color: white;
  }
  .btn-approve:hover {
    background: #43a047;
  }
  .btn-reject {
    background: #e53935;
    color: white;
    margin-left: 8px;
  }
  .btn-reject:hover {
    background: #d32f2f;
  }
</style>
@endsection
