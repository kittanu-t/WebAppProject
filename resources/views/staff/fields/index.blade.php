@extends('layouts.app')
@section('title','My Fields')

@section('content')
<h1>My Fields</h1>

@if(session('status')) <div class="p-2 bg-green-100">{{ session('status') }}</div> @endif
@if($errors->any())   <div class="p-2 bg-red-100">{{ $errors->first() }}</div>   @endif

@forelse($fields as $f)
  @php
    $fieldKey = 'field:'.$f->id;
    $fieldActive = ($activeClosures[$fieldKey] ?? collect())->first();
  @endphp

  <div style="border:1px solid #ddd; padding:12px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <strong>{{ $f->name }}</strong> ({{ $f->sport_type }})
        <div>Location: {{ $f->location }}</div>
        <div>Status: <strong>{{ $f->status }}</strong></div>
        @if($fieldActive)
          <div style="color:#b45309">
            ปิดทั้งสนาม: {{ $fieldActive->first()->reason ?? '-' }}
            <br>ช่วง: {{ $fieldActive->first()->start_datetime }} - {{ $fieldActive->first()->end_datetime }}
          </div>
        @endif
      </div>
      <div>
        <a href="{{ route('fields.show', $f->id) }}">ดูปฏิทินสนามนี้</a>
      </div>
    </div>

    {{-- ปุ่มปิด/เปิด "ทั้งสนาม" --}}
    <div style="margin-top:8px;">
      @if($f->status !== 'available')
        <form method="POST" action="{{ route('staff.fields.open', $f->id) }}" style="display:inline;">
          @csrf
          <button type="submit">เปิดสนาม (ทั้งก้อน)</button>
        </form>
      @else
        <form method="POST" action="{{ route('staff.fields.close', $f->id) }}" style="display:block; margin-top:6px;">
          @csrf
          <div>
            <label>เหตุผลปิดทั้งสนาม</label>
            <input type="text" name="reason" required placeholder="เช่น Maintenance / ทำความสะอาด">
          </div>
          <div style="margin-top:6px;">
            <label>สถานะ:</label>
            <select name="status">
              <option value="closed">closed</option>
              <option value="maintenance">maintenance</option>
            </select>
          </div>
          <div style="margin-top:6px;">
            <label>สิ้นสุดการปิด (เว้นว่าง = ปิดจนกว่าจะเปิดเอง)</label>
            <input type="datetime-local" name="end_datetime">
          </div>
          <button type="submit" style="margin-top:6px;">ปิดทั้งสนาม + ประกาศ</button>
        </form>
      @endif
    </div>

    {{-- ตารางคอร์ตทั้งหมด --}}
    <div style="margin-top:10px;">
      <h4>Units / Courts</h4>
      <table border="1" cellpadding="6" width="100%">
        <tr>
          <th style="text-align:left;">ชื่อคอร์ต</th>
          <th style="text-align:left;">สถานะ</th>
          <th style="text-align:left;">การทำงาน</th>
        </tr>
        @forelse($f->units as $u)
          @php
            $ukey = 'unit:'.$u->id;
            $uActive = ($activeClosures[$ukey] ?? collect())->first();
          @endphp
          <tr>
            <td>{{ $u->name }}</td>
            <td>
              <strong>{{ $u->status }}</strong>
              @if($uActive)
                <div style="color:#b45309; font-size:12px;">
                  ปิดชั่วคราว: {{ $uActive->first()->reason ?? '-' }}
                  <br>ช่วง: {{ $uActive->first()->start_datetime }} - {{ $uActive->first()->end_datetime }}
                </div>
              @endif
            </td>
            <td>
              @if($u->status !== 'available')
                <form method="POST" action="{{ route('staff.units.open', [$f->id, $u->id]) }}" style="display:inline;">
                  @csrf <button type="submit">เปิดคอร์ต</button>
                </form>
              @else
                <form method="POST" action="{{ route('staff.units.close', [$f->id, $u->id]) }}" style="display:inline;">
                  @csrf
                  <input type="text" name="reason" required placeholder="เหตุผล" style="width:180px;">
                  <select name="status">
                    <option value="closed">closed</option>
                    <option value="maintenance">maintenance</option>
                  </select>
                  <input type="datetime-local" name="end_datetime">
                  <button type="submit">ปิดคอร์ต + ประกาศ</button>
                </form>
              @endif

              <a href="{{ route('fields.show', $f->id) }}" style="margin-left:8px;">ดูปฏิทิน</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="3">ยังไม่มีคอร์ตในสนามนี้</td></tr>
        @endforelse
      </table>
    </div>
  </div>
@empty
  <p>คุณยังไม่ได้รับมอบหมายให้ดูแลสนามใด ๆ</p>
@endforelse
@endsection
