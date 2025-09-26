@extends('layouts.app')
@section('title','Create Announcement')
@section('content')
<h1>Create Announcement</h1>

<form method="POST" action="{{ route('admin.announcements.store') }}">
@csrf
<div>Title <input name="title" value="{{ old('title') }}" required></div>
<div>
  Audience
  <select name="audience" required>
    @foreach(['all','users','staff'] as $a)
      <option value="{{ $a }}" @selected(old('audience')===$a)>{{ $a }}</option>
    @endforeach
  </select>
</div>
<div>
  Published At (optional)
  <input type="datetime-local" name="published_at" value="{{ old('published_at') }}">
</div>
<div>
  Content
  <textarea name="content" rows="8" required>{{ old('content') }}</textarea>
</div>
<button type="submit">Save</button>
</form>
@endsection
