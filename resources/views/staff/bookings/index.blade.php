@extends('layouts.app') 

@section('title','Pending Bookings')

@section('content')
<h1>Pending Bookings</h1>

{{-- แสดงข้อความแจ้งเตือนเมื่อมีสถานะ (ส่งมาจาก Controller ผ่าน session) --}}
@if(session('status'))
    <div class="alert-success">
        {{ session('status') }}
    </div>
@endif

{{-- วนลูปแสดงรายการ booking ที่รับมาจาก Controller --}}
@forelse($bookings as $b)
  <div class="booking-card">
      <div class="booking-header">
          <strong>Booking #{{ $b->id }}</strong>
      </div>

      {{-- ข้อมูลสนามกีฬา ดึงจากความสัมพันธ์ sportsField ใน Model --}}
      <div><strong>Field:</strong> {{ $b->sportsField->name ?? '-' }}</div>

      {{-- ข้อมูลผู้จอง ดึงจากความสัมพันธ์ user ใน Model --}}
      <div><strong>User:</strong> {{ $b->user->name ?? '-' }} ({{ $b->user->email ?? '-' }})</div>

      {{-- วันที่และเวลา booking --}}
      <div><strong>Date:</strong> {{ $b->date }} {{ $b->start_time }} - {{ $b->end_time }}</div>

      {{-- แสดงสถานะ booking --}}
      <div>
        <strong>Status:</strong> 
        <span class="status-badge {{ $b->status }}">{{ ucfirst($b->status) }}</span>
      </div>

      {{-- ปุ่มอนุมัติและปฏิเสธการจอง --}}
      <div class="action-buttons">
          {{-- เมื่อกดปุ่มนี้ จะส่งข้อมูลแบบ POST ไปยัง route staff.bookings.approve --}}
          {{-- Controller จะรับข้อมูล booking ID แล้วอัปเดตสถานะในฐานข้อมูล --}}
          <form method="POST" action="{{ route('staff.bookings.approve', $b->id) }}" style="display:inline;">
              @csrf
              <button type="submit" class="btn-approve">Approve</button>
          </form>

          {{-- เมื่อกดปุ่มนี้ จะส่งข้อมูลแบบ POST ไปยัง route staff.bookings.reject --}}
          {{-- Controller จะอัปเดตสถานะ booking เป็น rejected --}}
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

@endsection
