@extends('layouts.app')
@section('title', $announcement->title)

@section('content')
<h1 style="background-color: rgba(255, 208, 0, 0.83); padding: 10px;">{{ $announcement->title }}</h1>
<p class="text-sm">Audience: {{ $announcement->audience }} | Published: {{ $announcement->published_at }}</p>
<hr>
<div>{!! nl2br(e($announcement->content)) !!}</div>
<p class="mt-3"><a href="{{ route('user.announcements.index') }}">← กลับรายการประกาศ</a></p>
@endsection
