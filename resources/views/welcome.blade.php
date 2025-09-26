@extends('layouts.app')

@section('title','Home')

@auth
@section('content')
<h2>Latest Announcement</h2>
@if(!empty($announcements) && count($announcements))
  @php $a = $announcements->first(); @endphp
  <div>
    <strong>[{{ strtoupper($a->audience) }}]</strong>
    {{ $a->title }} — <small>{{ $a->published_at }}</small>
  </div>
@else
  <p>ยังไม่มีประกาศ</p>
@endif
@endsection
@endauth
