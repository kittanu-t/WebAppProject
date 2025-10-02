@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h1 style="margin-bottom: 15px; background-color: #FFB900; color: #212529; padding: 10px; border-radius: 8px;">Admin Dashboard</h1>

<div class="card p-3 shadow-sm rounded-4 mb-3" style="border: 1px solid #6C757D;">
  <div style="margin-bottom: 10px;">
    <strong>Total Bookings:</strong> {{ $totalBookings }} |
  <strong>Total Users:</strong> {{ $totalUsers }} |
  <strong>Total Fields:</strong> {{ $totalFields }}
  </div>
</div>

<h3 style="margin-bottom: 10px;">Bookings by Status</h3>
<div class="card p-3 shadow-sm rounded-4 mb-3" style="border: 1px solid #6C757D;">
  <ul style="margin-bottom: 0;">
    @foreach($statusCounts as $status => $c)
      <li style="margin-bottom: 5px;">{{ ucfirst($status) }}: {{ $c }}</li>
    @endforeach
  </ul>
</div>

<h3 style="margin-bottom: 10px;">Top 5 Fields by Booking Count</h3>
<div class="card p-3 shadow-sm rounded-4 mb-3" style="border: 1px solid #6C757D;">
  <table border="1" cellpadding="4" width="100%" class="table table-striped table-hover" style="border: 1px solid #6C757D; border-radius: 8px;">
    <tr>
      <th style="padding: 6px;">Field</th>
      <th style="padding: 6px;">Bookings</th>
    </tr>
    @foreach($topFields as $row)
      <tr>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;">{{ $row->sportsField->name ?? '-' }}</td>
        <td style="padding: 6px; border-bottom: 1px solid #6C757D;">{{ $row->c }}</td>
      </tr>
    @endforeach
  </table>
</div>

<h3 style="margin-bottom: 10px;">Utilization (last 30 days)</h3>
<div class="card p-3 shadow-sm rounded-4" style="border: 1px solid #6C757D;">
  <p>{{ $utilization }} bookings/field (avg over 30 days)</p>
</div>
@endsection