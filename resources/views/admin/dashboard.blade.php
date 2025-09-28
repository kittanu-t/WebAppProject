@extends('layouts.app')
@section('title','Admin Dashboard')

@section('content')
<h1>Admin Dashboard</h1>

<div style="margin-bottom:20px;">
  <strong>Total Bookings:</strong> {{ $totalBookings }} |
  <strong>Total Users:</strong> {{ $totalUsers }} |
  <strong>Total Fields:</strong> {{ $totalFields }}
</div>

<h3>Bookings by Status</h3>
<ul>
  @foreach($statusCounts as $status=>$c)
    <li>{{ ucfirst($status) }}: {{ $c }}</li>
  @endforeach
</ul>

<h3>Top 5 Fields by Booking Count</h3>
<table border="1" cellpadding="6">
  <tr><th>Field</th><th>Bookings</th></tr>
  @foreach($topFields as $row)
    <tr>
      <td>{{ $row->sportsField->name ?? '-' }}</td>
      <td>{{ $row->c }}</td>
    </tr>
  @endforeach
</table>

<h3>Utilization (last 30 days)</h3>
<p>{{ $utilization }} bookings/field (avg over 30 days)</p>

@endsection
