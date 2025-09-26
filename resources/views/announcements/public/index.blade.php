@extends('layouts.app')
@section('title','Announcements')

@section('content')
<h1>Announcements</h1>

<form method="GET" class="mb-2">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาหัวข้อ/เนื้อหา">
  <button>ค้นหา</button>
</form>

@forelse($announcements as $a)
  <div class="border p-3 mb-3">
    <div><strong>{{ $a->title }}</strong></div>
    <div class="text-sm">Audience: {{ $a->audience }} | Published: {{ $a->published_at }}</div>
    <div class="mt-1">
      <a href="{{ route('user.announcements.show', $a) }}">อ่านต่อ</a>
    </div>
  </div>
@empty
  <p>ยังไม่มีประกาศ</p>
@endforelse

{{ $announcements->links() }}
@endsection
