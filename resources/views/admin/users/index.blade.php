@extends('layouts.app')
@section('title', 'Users')
@section('content')
<h1 style="margin-bottom: 15px;">Users</h1>

<div class="d-flex align-items-center mb-3">
    <form method="GET" class="d-flex align-items-center" style="margin-right: 10px;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name/email" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 5px 10px;">
        <button type="submit" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 5px 10px; margin-left: 5px;">Search</button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 5px 10px; text-decoration: none;">+ Create</a>
</div>

@if(session('status')) <div class="alert alert-info mb-3">{{ session('status') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger mb-3">{{ $errors->first() }}</div> @endif

<div class="card p-3 shadow-sm rounded-4" style="border: 1px solid #6C757D;">
  <table border="1" cellpadding="4" width="100%" class="table table-striped table-hover" style="border: 1px solid #6C757D; border-radius: 8px;">
    <tr>
      <th style="padding: 6px;">ID</th>
      <th style="padding: 6px;">Name</th>
      <th style="padding: 6px;">Email</th>
      <th style="padding: 6px;">Role</th>
      <th style="padding: 6px;">Active</th>
      <th style="padding: 6px;">Actions</th>
    </tr>
    @foreach($users as $u)
      <tr>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;">{{ $u->id }}</td>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;"><a href="{{ route('admin.users.show', $u) }}" style="color: #212529; text-decoration: none;">{{ $u->name }}</a></td>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;">{{ $u->email }}</td>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;">{{ $u->role }}</td>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;">{{ $u->active ? 'Yes' : 'No' }}</td>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;">
          <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 2px 6px; text-decoration: none;">Edit</a>
          <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm fw-bold" style="background-color: #E54D42; color: #FFFFFF; border: none; border-radius: 8px; padding: 2px 6px;">Delete</button>
          </form>
        </td>
      </tr>
    @endforeach
  </table>
</div>

{{ $users->links() }}
@endsection