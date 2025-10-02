@extends('layouts.app')
@section('title', 'All Bookings')
@section('content')
<h1 style="margin-bottom: 15px; background-color: #FFB900; color: #212529; padding: 10px; border-radius: 8px;">All Bookings</h1>

@if(session('status')) <div class="alert alert-info mb-4">{{ session('status') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger mb-4">{{ $errors->first() }}</div> @endif

{{-- Filter --}}
<form method="GET" class="mb-2 d-flex align-items-center">
  <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px; margin-right: 5px;">
  <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px; margin-right: 5px;">
  <select name="status" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px; margin-right: 5px;">
    <option value="">-- status --</option>
    @foreach($statuses as $s)
      <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
    @endforeach
  </select>
  <select name="field_id" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px; margin-right: 5px;">
    <option value="">-- field --</option>
    @foreach($fields as $f)
      <option value="{{ $f->id }}" @selected((string)request('field_id')===(string)$f->id)>{{ $f->name }}</option>
    @endforeach
  </select>
  <input type="text" name="q" value="{{ request('q') }}" placeholder="user name/email" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px; margin-right: 5px;">
  <button type="submit" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 6px 12px; margin-right: 5px;">Filter</button>
  <a href="{{ route('admin.bookings.index') }}" class="btn fw-bold" style="background-color: #E54D42; color: #FFFFFF; border: none; border-radius: 8px; padding: 6px 12px; text-decoration: none;">Reset</a>
</form>

<table border="1" cellpadding="6" width="100%" style="border: 1px solid #6C757D; border-radius: 8px;">
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
      <td class="d-flex gap-1">
        {{-- ฟอร์มเปลี่ยนสถานะแบบรวดเร็ว --}}
        <form method="POST" action="{{ route('admin.bookings.updateStatus', $b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="approved">
          <button @disabled($b->status==='approved') class="btn btn-sm fw-bold" style="background-color: #28A745; color: #FFFFFF; border: none; border-radius: 8px; padding: 4px 8px;">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus', $b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="rejected">
          <button @disabled($b->status==='rejected') class="btn btn-sm fw-bold" style="background-color: #DC3545; color: #FFFFFF; border: none; border-radius: 8px; padding: 4px 8px;">Reject</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus', $b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="cancelled">
          <button @disabled($b->status==='cancelled') class="btn btn-sm fw-bold" style="background-color: #FFC107; color: #FFFFFF; border: none; border-radius: 8px; padding: 4px 8px;">Cancel</button>
        </form>
        <form method="POST" action="{{ route('admin.bookings.updateStatus', $b->id) }}" style="display:inline;">
          @csrf
          <input type="hidden" name="status" value="completed">
          <button @disabled($b->status==='completed') class="btn btn-sm fw-bold" style="background-color: #4A90E2; color: #FFFFFF; border: none; border-radius: 8px; padding: 4px 8px;">Complete</button>
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