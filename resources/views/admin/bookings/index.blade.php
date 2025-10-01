@extends('layouts.app')
@section('title', 'All Bookings')
@section('content')
<h1>All Bookings</h1>

@if(session('status')) <div class="p-2 bg-green-100">{{ session('status') }}</div> @endif
@if($errors->any()) <div class="p-2 bg-red-100">{{ $errors->first() }}</div> @endif

<form method="GET" class="mb-3" style="margin-bottom:12px; width: 100%;">
  <input type="date" name="date_from" value="{{ request('date_from') }}" style="border: 1px solid #4A90E2; border-radius: 8px; margin-right: 5px;">
  <input type="date" name="date_to" value="{{ request('date_to') }}" style="border: 1px solid #4A90E2; border-radius: 8px; margin-right: 5px;">
  <select name="status" style="border: 1px solid #4A90E2; border-radius: 8px; margin-right: 5px;">
    <option value="">-- status --</option>
    @foreach($statuses as $s)
      <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
    @endforeach
  </select>
  <select name="field_id" style="border: 1px solid #4A90E2; border-radius: 8px; margin-right: 5px;">
    <option value="">-- field --</option>
    @foreach($fields as $f)
      <option value="{{ $f->id }}" @selected((string)request('field_id')===(string)$f->id)>{{ $f->name }}</option>
    @endforeach
  </select>
  <input type="text" name="q" value="{{ request('q') }}" placeholder="user name/email" style="border: 1px solid #4A90E2; border-radius: 8px; margin-right: 5px;">
  <button type="submit" style="background-color: #4A90E2; color: #FFFFFF; border: 1px solid #4A90E2; border-radius: 8px; padding: 5px 10px;">Filter</button>
  <a href="{{ route('admin.bookings.index') }}" style="background-color: #4A90E2; color: #FFFFFF; border: 1px solid #4A90E2; border-radius: 8px; padding: 5px 10px; text-decoration: none;">Reset</a>
</form>

<table border="1" cellpadding="6" width="100%" style="border: 2px solid #4A90E2; border-radius: 12px; overflow: hidden;">
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
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="approved">
          <button @disabled($b->status==='approved') style="background-color: #28A745; color: #FFFFFF; border: 2px solid #28A745; border-radius: 12px; padding: 2px 6px;">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="rejected">
          <button @disabled($b->status==='rejected') style="background-color: #DC3545; color: #FFFFFF; border: 2px solid #DC3545; border-radius: 12px; padding: 2px 6px;">Reject</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="cancelled">
          <button @disabled($b->status==='cancelled') style="background-color: #FFC107; color: #FFFFFF; border: 2px solid #FFC107; border-radius: 12px; padding: 2px 6px;">Cancel</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="completed">
          <button @disabled($b->status==='completed') style="background-color: #6C757D; color: #FFFFFF; border: 2px solid #6C757D; border-radius: 12px; padding: 2px 6px;">Complete</button>
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