@extends('layouts.app')
@section('title','Users')
@section('content')
<h1>Users</h1>

<form method="GET" class="mb-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name/email">
    <button>Search</button>
    <a href="{{ route('admin.users.create') }}">+ Create</a>
</form>

@if(session('status')) <div>{{ session('status') }}</div> @endif
@if($errors->any()) <div>{{ $errors->first() }}</div> @endif

<table border="1" cellpadding="6">
  <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th></th></tr>
  @foreach($users as $u)
    <tr>
      <td>{{ $u->id }}</td>
      <td><a href="{{ route('admin.users.show',$u) }}">{{ $u->name }}</a></td>
      <td>{{ $u->email }}</td>
      <td>{{ $u->role }}</td>
      <td>{{ $u->active ? 'Yes' : 'No' }}</td>
      <td>
        <a href="{{ route('admin.users.edit',$u) }}">Edit</a>
        <form method="POST" action="{{ route('admin.users.destroy',$u) }}" style="display:inline" onsubmit="return confirm('Delete?')">
          @csrf @method('DELETE')
          <button>Delete</button>
        </form>
      </td>
    </tr>
  @endforeach
</table>

{{ $users->links() }}
@endsection
