@extends('layouts.app')

@section('title','Fields')

@section('content')
<h1>Sports Fields</h1>

{{-- ฟอร์มค้นหา/กรอง --}}
<form method="GET" class="mb-3" style="margin-bottom:16px;">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อสนาม/ที่ตั้ง">
    <select name="sport_type">
        <option value="">-- ชนิดกีฬา --</option>
        @foreach($types as $t)
            <option value="{{ $t }}" @selected(request('sport_type')===$t)>{{ $t }}</option>
        @endforeach
    </select>
    <label style="margin-left:8px;">
        <input type="checkbox" name="only_available" value="1" @checked(request('only_available'))>
        เฉพาะที่เปิดใช้งาน
    </label>
    <button type="submit">ค้นหา</button>
</form>

{{-- ตารางรายการสนาม --}}
<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <tr>
        <th style="text-align:left;">ชื่อสนาม</th>
        <th style="text-align:left;">กีฬา</th>
        <th style="text-align:left;">ที่ตั้ง</th>
        <th style="text-align:left;">ความจุ</th>
        <th style="text-align:left;">สถานะ</th>
        <th style="text-align:left;">การทำงาน</th>
    </tr>

    @forelse($fields as $f)
        <tr>
            <td>{{ $f->name }}</td>
            <td>{{ $f->sport_type }}</td>
            <td>{{ $f->location }}</td>
            <td>{{ $f->capacity }}</td>
            <td>{{ $f->status }}</td>
            <td>
                <a href="{{ route('fields.show', $f->id) }}">ดูปฏิทิน</a>
                @auth
                    @if(auth()->user()->role === 'user')
                        | <a href="{{ route('bookings.create', ['field_id' => $f->id]) }}">จองสนามนี้</a>
                    @endif
                @endauth
            </td>
        </tr>
    @empty
        <tr><td colspan="6">ยังไม่มีข้อมูลสนาม</td></tr>
    @endforelse
</table>

<div style="margin-top:12px;">
    {{ $fields->links() }}
</div>
@endsection
