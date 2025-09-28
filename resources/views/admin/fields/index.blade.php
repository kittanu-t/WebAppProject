@extends('layouts.app')
@section('title','Fields')
@section('content')
<h1>Fields</h1>

<form method="GET" class="mb-2">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name/type/location">
  <button>Search</button>
  <a href="{{ route('admin.fields.create') }}">+ Create Field</a>
</form>

@if(session('status')) <div>{{ session('status') }}</div> @endif

<table border="1" cellpadding="6" width="100%">
  <tr>
    <th>ID</th><th>Name</th><th>Type</th><th>Location</th>
    <th>Status</th><th>Owner</th><th>Units</th><th></th>
  </tr>
  @foreach($fields as $f)
  <tr>
    <td>{{ $f->id }}</td>
    <td>{{ $f->name }}</td>
    <td>{{ $f->sport_type }}</td>
    <td>{{ $f->location }}</td>
    <td>{{ $f->status }}</td>
    <td>{{ $f->owner?->name ?? '-' }}</td>
    <td>{{ $f->units_count }}</td>
    <td>
      <a href="{{ route('admin.fields.edit',$f) }}">Edit</a>
      <a href="{{ route('admin.fields.units.index',$f) }}">Manage Units</a>
      <form method="POST" action="{{ route('admin.fields.destroy',$f) }}" style="display:inline" onsubmit="return confirm('Delete field?')">
        @csrf @method('DELETE')
        <button>Delete</button>
      </form>
    </td>
  </tr>
  @endforeach
</table>

{{ $fields->links() }}
@endsection
