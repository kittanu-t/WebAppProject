@extends('layouts.app')
@section('title', 'Manage Units - '.$field->name)
@section('content')
<h1>Manage Units — {{ $field->name }}</h1>
<p><a href="{{ route('admin.fields.index') }}" class="btn fw-bold" style="background-color: #6C757D; color: #FFFFFF; border: none; border-radius: 8px; padding: 6px 12px; text-decoration: none;">← Back to Fields</a></p>

@if(session('status')) <div class="alert alert-info mb-4">{{ session('status') }}</div> @endif

<p><a href="{{ route('admin.fields.units.create', $field) }}" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 6px 12px; text-decoration: none;">+ Add Unit</a></p>

<div class="card p-4 shadow-sm rounded-4" style="border: 1px solid #6C757D;">
  <table border="1" cellpadding="6" width="100%" class="table table-striped table-hover" style="border: 1px solid #6C757D; border-radius: 8px;">
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
        <a href="{{ route('admin.fields.units.edit', [$field, $u]) }}" class="btn btn-sm fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 4px 8px; text-decoration: none;">Edit</a>
        <form method="POST" action="{{ route('admin.fields.units.destroy', [$field, $u]) }}" style="display:inline" onsubmit="return confirm('Delete unit?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm fw-bold" style="background-color: #E54D42; color: #FFFFFF; border: none; border-radius: 8px; padding: 4px 8px;">Delete</button>
        </form>
      </td>
    </tr>
    @endforeach
  </table>
</div>

{{ $units->links() }}
@endsection