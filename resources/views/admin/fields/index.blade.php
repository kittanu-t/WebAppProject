@extends('layouts.app')
@section('title', 'Fields')
@section('content')
<h1>Fields</h1>

<form method="GET" class="mb-2">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name/type/location" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;">
  <button type="submit" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 6px 12px; margin-top: 5px;">Search</button>
  <a href="{{ route('admin.fields.create') }}" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 6px 12px; text-decoration: none; margin-top: 5px;">+ Create Field</a>
</form>

@if(session('status')) <div class="alert alert-info mb-4">{{ session('status') }}</div> @endif

<table border="1" cellpadding="6" width="100%" style="border: 1px solid #6C757D; border-radius: 8px;">
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
    <td class="d-flex gap-1">
      <a href="{{ route('admin.fields.edit', $f) }}" class="btn btn-sm fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 4px 8px; text-decoration: none;">Edit</a>
      <a href="{{ route('admin.fields.units.index', $f) }}" class="btn btn-sm fw-bold" style="background-color: #6C757D; color: #FFFFFF; border: none; border-radius: 8px; padding: 4px 8px; text-decoration: none;">Manage Units</a>
      <form method="POST" action="{{ route('admin.fields.destroy', $f) }}" style="display:inline" onsubmit="return confirm('Delete field?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm fw-bold" style="background-color: #E54D42; color: #FFFFFF; border: none; border-radius: 8px; padding: 4px 8px;">Delete</button>
      </form>
    </td>
  </tr>
  @endforeach
</table>

{{ $fields->links() }}
@endsection