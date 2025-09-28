@extends('layouts.app')
@section('title','Manage Units - '.$field->name)

@section('content')
<h1>Manage Units — {{ $field->name }}</h1>
<p><a href="{{ route('admin.fields.index') }}">← Back to Fields</a></p>

@if(session('status')) <div class="p-2 bg-green-100">{{ session('status') }}</div> @endif

<p><a href="{{ route('admin.fields.units.create',$field) }}">+ Add Unit</a></p>

<table border="1" cellpadding="6" width="100%">
  <tr>
    <th>#</th><th>Name</th><th>Status</th><th>Capacity</th><th></th>
  </tr>
  @foreach($units as $u)
  <tr>
    <td>{{ $u->index }}</td>
    <td>{{ $u->name }}</td>
    <td>{{ $u->status }}</td>
    <td>{{ $u->capacity }}</td>
    <td>
      <a href="{{ route('admin.fields.units.edit', [$field,$u]) }}">Edit</a>
      <form method="POST" action="{{ route('admin.fields.units.destroy', [$field,$u]) }}" style="display:inline" onsubmit="return confirm('Delete unit?')">
        @csrf @method('DELETE')
        <button>Delete</button>
      </form>
    </td>
  </tr>
  @endforeach
</table>

{{ $units->links() }}
@endsection
