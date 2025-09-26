@extends('layouts.app')
@section('title','Fields')
@section('content')
<h1>Fields</h1>

<form method="GET" class="mb-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name/type/location">
    <button>Search</button>
    <a href="{{ route('admin.fields.create') }}">+ Create</a>
</form>

@if(session('status')) <div>{{ session('status') }}</div> @endif
@if($errors->any()) <div>{{ $errors->first() }}</div> @endif

<table border="1" cellpadding="6" width="100%">
  <tr>
    <th>ID</th><th>Name</th><th>Type</th><th>Location</th><th>Status</th><th>Owner</th><th></th>
  </tr>
  @foreach($fields as $f)
    <tr>
      <td>{{ $f->id }}</td>
      <td><a href="{{ route('admin.fields.show',$f) }}">{{ $f->name }}</a></td>
      <td>{{ $f->sport_type }}</td>
      <td>{{ $f->location }}</td>
      <td>{{ $f->status }}</td>
      <td>{{ $f->owner?->name ?? '-' }}</td>
      <td>
        <a href="{{ route('admin.fields.edit',$f) }}">Edit</a>
        <form method="POST" action="{{ route('admin.fields.destroy',$f) }}" style="display:inline" onsubmit="return confirm('Delete?')">
          @csrf @method('DELETE')
          <button>Delete</button>
        </form>
      </td>
    </tr>
  @endforeach
</table>

{{ $fields->links() }}
@endsection
