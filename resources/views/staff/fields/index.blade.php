@extends('layouts.app')

@section('title','My Fields')

@section('content')
<h1>My Fields</h1>

@if(session('status'))
  <div class="p-2 bg-green-100">{{ session('status') }}</div>
@endif
@if($errors->any())
  <div class="p-2 bg-red-100">{{ $errors->first() }}</div>
@endif

@forelse($fields as $f)
  @php
    $active = ($activeClosures[$f->id] ?? collect())->first();
  @endphp
  <div style="border:1px solid #ddd; padding:12px; margin-bottom:12px;">
    <div><strong>{{ $f->name }}</strong> ({{ $f->sport_type }})</div>
    <div>Location: {{ $f->location }}</div>
    <div>Status: <strong>{{ $f->status }}</strong></div>
    @if($active)
      <div style="color:#b45309;">
        ปิดชั่วคราว: {{ $active->reason ?? '-' }}<br>
        ระยะเวลา: {{ $active->start_datetime }} - {{ $active->end_datetime }}
      </div>
    @endif

    <div style="margin-top:8px;">
      {{-- ปิดสนามชั่วคราว --}}
      @if($f->status !== 'available')
        {{-- แสดงปุ่มเปิดสนาม ถ้าตอนนี้ไม่ได้ available --}}
        <form method="POST" action="{{ route('staff.fields.open', $f->id) }}" style="display:inline;">
          @csrf
          <button type="submit">เปิดสนาม</button>
        </form>
      @else
        {{-- ฟอร์มปิดสนาม --}}
        <form method="POST" action="{{ route('staff.fields.close', $f->id) }}" style="display:block; margin-top:8px;">
          @csrf
          <div>
            <label>เหตุผลการปิด</label>
            <input type="text" name="reason" required placeholder="เช่น Maintenance / อุบัติเหตุ / ทำความสะอาด">
          </div>
          <div style="margin-top:6px;">
            <label>สถานะ:</label>
            <select name="status">
              <option value="closed">closed</option>
              <option value="maintenance">maintenance</option>
            </select>
          </div>
          <div style="margin-top:6px;">
            <label>สิ้นสุดการปิด (ไม่ระบุ = ปิดจนกว่าจะเปิดเอง)</label>
            <input type="datetime-local" name="end_datetime">
          </div>
          <div style="margin-top:6px;">
            <button type="submit">ปิดสนามชั่วคราว + สร้างประกาศ</button>
          </div>
        </form>
      @endif

      {{-- ลิงก์ดูตารางสนามในปฏิทิน --}}
      <div style="margin-top:6px;">
        <a href="{{ route('fields.show', $f->id) }}">ดูปฏิทินของสนามนี้</a>
      </div>
    </div>
  </div>
@empty
  <p>คุณยังไม่ได้รับมอบหมายให้ดูแลสนามใด ๆ</p>
@endforelse
@endsection
