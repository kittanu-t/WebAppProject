@extends('layouts.app')
@section('title','Announcements')

@section('styles')
<style>
  .submit-button {
    background-color: rgba(255, 0, 0, 0.77);
    padding: 10px 20px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
  }

  .submit-button:hover {
    background-color: green;
    color: white;
  }
</style>

@section('content')
<h1 style="background-color: rgb(255, 187, 0); padding: 10px;">Announcements</h1>

<form method="GET" class="mb-2">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาหัวข้อ/เนื้อหา">
  <button class="submit-button">ค้นหา</button>
</form>

@forelse($announcements as $a)
  <div class="border p-3 mb-3">
    <div><strong style="background-color: rgb(255, 238, 0); padding: 3px;">{{ $a->title }}</strong></div>
    <div class="text-sm">Audience: {{ $a->audience }} | Published: {{ $a->published_at }}</div>
    <div class="mt-1">
      <a href="{{ route('user.announcements.show', $a) }}">อ่านต่อ</a>
    </div>
  </div>
@empty
  <p style="color: red;">ยังไม่มีประกาศ</p>
@endforelse

{{ $announcements->links() }}
@endsection
