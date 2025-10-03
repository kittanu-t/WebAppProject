@extends('layouts.app') 
@section('title','My Fields')

@section('content')
<style>
  .card {
    background: #fff;
    padding: 16px;
    margin-bottom: 18px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }
  h1 {
    font-weight: bold;
    margin-bottom: 18px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: #fff;
  }
  th, td {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
    text-align: left;
  }
  th {
    background: #f9d71c;
    color: #222;
  }
  .btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
  }
  .btn-yellow {
    background: #f9d71c;
    color: #000;
  }
  .btn-red {
    background: #e63946;
    color: #fff;
  }
  .btn-gray {
    background: #ddd;
    color: #333;
  }
  .status-alert {
    color: #b45309;
    font-size: 13px;
    margin-top: 4px;
  }
</style>

<h1>My Fields</h1>

@if(session('status')) 
  <div class="p-2 bg-green-100">{{ session('status') }}</div> 
@endif
@if($errors->any())   
  <div class="p-2 bg-red-100">{{ $errors->first() }}</div>   
@endif

@forelse($fields as $f)
  @php
    $fieldKey = 'field:'.$f->id;
    $fieldActive = ($activeClosures[$fieldKey] ?? collect())->first();
  @endphp

  <div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
      <div>
        <strong>{{ $f->name }}</strong> ({{ $f->sport_type }})
        <div>Location: {{ $f->location }}</div>
        <div>Status: <strong>{{ $f->status }}</strong></div>
        @if($fieldActive)
          <div class="status-alert">
            ปิดทั้งสนาม: {{ $fieldActive->first()->reason ?? '-' }} <br>
            ช่วง: {{ $fieldActive->first()->start_datetime }} - {{ $fieldActive->first()->end_datetime }}
          </div>
        @endif
      </div>
      <div>
        <a href="{{ route('fields.show', $f->id) }}" class="btn btn-gray">ดูปฏิทินสนามนี้</a>
      </div>
    </div>

    {{-- ปุ่มปิด/เปิด "ทั้งสนาม" --}}
    <div style="margin-top:10px;">
      @if($f->status !== 'available')
        <form method="POST" action="{{ route('staff.fields.open', $f->id) }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn btn-yellow">เปิดสนาม (ทั้งก้อน)</button>
        </form>
      @else
        <form method="POST" action="{{ route('staff.fields.close', $f->id) }}" style="margin-top:8px;">
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
          <button type="submit" class="btn btn-red" style="margin-top:8px;">ปิดทั้งสนาม + ประกาศ</button>
        </form>
      @endif
    </div>

    {{-- ตารางคอร์ตทั้งหมด --}}
    <div style="margin-top:12px;">
      <h4>Units / Courts</h4>
      <table>
        <tr>
          <th>ชื่อคอร์ต</th>
          <th>สถานะ</th>
          <th>การทำงาน</th>
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
                <div class="status-alert">
                  ปิดชั่วคราว: {{ $uActive->first()->reason ?? '-' }} <br>
                  ช่วง: {{ $uActive->first()->start_datetime }} - {{ $uActive->first()->end_datetime }}
                </div>
              @endif
            </td>
            <td>
              @if($u->status !== 'available')
                <form method="POST" action="{{ route('staff.units.open', [$f->id, $u->id]) }}" style="display:inline;">
                  @csrf 
                  <button type="submit" class="btn btn-yellow">เปิดคอร์ต</button>
                </form>
              @else
                <form method="POST" action="{{ route('staff.units.close', [$f->id, $u->id]) }}" style="display:inline;">
                  @csrf
                  <input type="text" name="reason" required placeholder="เหตุผล" style="width:160px;">
                  <select name="status">
                    <option value="closed">closed</option>
                    <option value="maintenance">maintenance</option>
                  </select>
                  <input type="datetime-local" name="end_datetime">
                  <button type="submit" class="btn btn-red">ปิดคอร์ต + ประกาศ</button>
                </form>
              @endif

              <a href="{{ route('fields.show', $f->id) }}" class="btn btn-gray" style="margin-left:6px;">ดูปฏิทิน</a>
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
