@extends('layouts.app')
@section('title','Announcements')
@section('content')
<h1>Announcements</h1>

<form method="GET" class="mb-2">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title/content">
  <select name="audience">
    <option value="">-- audience --</option>
    @foreach(['all','users','staff'] as $a)
      <option value="{{ $a }}" @selected(request('audience')===$a)>{{ $a }}</option>
    @endforeach
  </select>
  <button>Filter</button>
  <a href="{{ route('admin.announcements.create') }}">+ Create</a>
</form>

@if(session('status')) <div>{{ session('status') }}</div> @endif

<table border="1" cellpadding="6">
  <tr>
    <th>ID</th><th>Title</th><th>Audience</th><th>Published</th><th>By</th><th></th>
  </tr>
  @foreach($announcements as $a)
    <tr>
      <td>{{ $a->id }}</td>
      <td><a href="{{ route('admin.announcements.show',$a) }}">{{ $a->title }}</a></td>
      <td>{{ $a->audience }}</td>
      <td>{{ $a->published_at }}</td>
      <td>{{ $a->creator?->name }}</td>
      <td>
        <a href="{{ route('admin.announcements.edit',$a) }}">Edit</a>
        <form method="POST" action="{{ route('admin.announcements.destroy',$a) }}" style="display:inline" onsubmit="return confirm('Delete?')">
          @csrf @method('DELETE')
          <button>Delete</button>
        </form>
      </td>
    </tr>
  @endforeach
</table>

{{ $announcements->links() }}
@endsection
