@extends('layouts.app')
@section('title', $announcement->title)

@section('content')
<h1>{{ $announcement->title }}</h1>
<p class="text-sm">Audience: {{ $announcement->audience }} | Published: {{ $announcement->published_at }}</p>
<hr>
<div>{!! nl2br(e($announcement->content)) !!}</div>
<p class="mt-3"><a href="{{ route('user.announcements.index') }}">← กลับรายการประกาศ</a></p>
@endsection
