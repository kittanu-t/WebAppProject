@extends('layouts.app')
@section('title','Field Detail')
@section('content')
<h1>Field #{{ $field->id }}</h1>
<p><strong>Name:</strong> {{ $field->name }}</p>
<p><strong>Type:</strong> {{ $field->sport_type }}</p>
<p><strong>Location:</strong> {{ $field->location }}</p>
<p><strong>Status:</strong> {{ $field->status }}</p>
<p><strong>Owner:</strong> {{ $field->owner?->name ?? '-' }}</p>
<p><strong>Min/Max Duration:</strong> {{ $field->min_duration_minutes }} / {{ $field->max_duration_minutes }} minutes</p>
<p><strong>Lead Time:</strong> {{ $field->lead_time_hours }} hours</p>
<p><a href="{{ route('admin.fields.edit',$field) }}">Edit</a></p>
@endsection
