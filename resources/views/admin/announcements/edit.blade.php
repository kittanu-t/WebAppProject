@extends('layouts.app')
@section('title','Edit Announcement')
@section('content')
<h1>Edit Announcement #{{ $announcement->id }}</h1>

<form method="POST" action="{{ route('admin.announcements.update',$announcement) }}">
@csrf @method('PUT')
<div>Title <input name="title" value="{{ old('title',$announcement->title) }}" required></div>
<div>
  Audience
  <select name="audience" required>
    @foreach(['all','users','staff'] as $a)
      <option value="{{ $a }}" @selected(old('audience',$announcement->audience)===$a)>{{ $a }}</option>
    @endforeach
  </select>
</div>
<div>
  Published At (optional)
  <input type="datetime-local" name="published_at"
         value="{{ old('published_at', optional($announcement->published_at)->format('Y-m-d\TH:i')) }}">
</div>
<div>
  Content
  <textarea name="content" rows="8" required>{{ old('content',$announcement->content) }}</textarea>
</div>
<button type="submit">Update</button>
</form>
@endsection
