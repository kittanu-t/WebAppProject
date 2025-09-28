@extends('layouts.app')
@section('title','All Bookings')
@section('content')
<h1>All Bookings</h1>

@if(session('status')) <div class="p-2 bg-green-100">{{ session('status') }}</div> @endif
@if($errors->any()) <div class="p-2 bg-red-100">{{ $errors->first() }}</div> @endif

{{-- Filter --}}
<form method="GET" class="mb-3" style="margin-bottom:12px;">
  <input type="date" name="date_from" value="{{ request('date_from') }}">
  <input type="date" name="date_to" value="{{ request('date_to') }}">
  <select name="status">
    <option value="">-- status --</option>
    @foreach($statuses as $s)
      <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
    @endforeach
  </select>
  <select name="field_id">
    <option value="">-- field --</option>
    @foreach($fields as $f)
      <option value="{{ $f->id }}" @selected((string)request('field_id')===(string)$f->id)>{{ $f->name }}</option>
    @endforeach
  </select>
  <input type="text" name="q" value="{{ request('q') }}" placeholder="user name/email">
  <button type="submit">Filter</button>
  <a href="{{ route('admin.bookings.index') }}">Reset</a>
</form>

<table border="1" cellpadding="6" width="100%">
  <tr>
    <th>ID</th>
    <th>Field</th>
    <th>User</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Actions</th>
  </tr>
  @forelse($bookings as $b)
    <tr>
      <td>#{{ $b->id }}</td>
      <td>{{ $b->sportsField->name ?? '-' }}</td>
      <td>{{ $b->user->name ?? '-' }} ({{ $b->user->email ?? '-' }})</td>
      <td>{{ $b->date }}</td>
      <td>{{ $b->start_time }} - {{ $b->end_time }}</td>
      <td><strong>{{ $b->status }}</strong></td>
      <td>
        {{-- ฟอร์มเปลี่ยนสถานะแบบรวดเร็ว --}}
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="approved">
          <button @disabled($b->status==='approved')>Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="rejected">
          <button @disabled($b->status==='rejected')>Reject</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="cancelled">
          <button @disabled($b->status==='cancelled')>Cancel</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="completed">
          <button @disabled($b->status==='completed')>Complete</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="7">ไม่พบข้อมูล</td></tr>
  @endforelse
</table>

<div style="margin-top:10px;">
  {{ $bookings->links() }}
</div>
@endsection
