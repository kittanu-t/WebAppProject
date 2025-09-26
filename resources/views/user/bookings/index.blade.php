@extends('layouts.app')
@section('title','My Bookings')

@section('content')
<h1>My Bookings</h1>

@forelse($bookings as $b)
  <div class="border p-3 mb-3">
      <div><strong>#{{ $b->id }}</strong> — {{ $b->sportsField->name ?? '-' }}</div>
      <div>{{ $b->date }} ({{ $b->start_time }} - {{ $b->end_time }}) — <strong>{{ ucfirst($b->status) }}</strong></div>

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
