@extends('layouts.app')
@section('title','Announcement Detail')
@section('content')
<h1>{{ $announcement->title }}</h1>
<p><strong>Audience:</strong> {{ $announcement->audience }}</p>
<p><strong>Published:</strong> {{ $announcement->published_at }}</p>
<p><strong>By:</strong> {{ $announcement->creator?->name }}</p>
<hr>
<div>{!! nl2br(e($announcement->content)) !!}</div>
@endsection
