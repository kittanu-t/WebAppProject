@extends('layouts.app')

@section('title','Fields')

@section('content')

<style>
    h1 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #222;
    }

    form input[type="text"], 
    form select {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-right: 6px;
    }

    form button {
        background-color: #f4c400; /* เหลือง */
        color: #000;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }
    form button:hover {
        background-color: #e0b200;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    table th {
        background: #f4c400; /* เหลือง */
        color: #000;
        padding: 10px;
        text-align: left;
    }

    table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    table tr:nth-child(even) {
        background: #fafafa;
    }

    table tr:hover {
        background: #fff6e5;
    }

    a {
        text-decoration: none;
        font-weight: bold;
    }
    a[href*="show"] {
        color: #d9534f; /* แดง */
    }
    a[href*="bookings"] {
        color: #f4c400; /* เหลือง */
    }

    .pagination {
        margin-top: 16px;
    }
    .pagination span, .pagination a {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        background: #fff;
        border: 1px solid #ddd;
        color: #333;
        transition: 0.3s;
    }
    .pagination a:hover {
        background: #f4c400;
        color: #000;
    }
    .pagination .active {
        background: #d9534f; /* แดง */
        color: #fff;
        border: none;
    }
</style>

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
<table>
    <tr>
        <th>ชื่อสนาม</th>
        <th>กีฬา</th>
        <th>ที่ตั้ง</th>
        <th>ความจุ</th>
        <th>สถานะ</th>
        <th>การทำงาน</th>
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
