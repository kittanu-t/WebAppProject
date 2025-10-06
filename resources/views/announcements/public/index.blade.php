@extends('layouts.app')
@section('title','Announcements')

@section('styles')
<style>
  :root{
      --bg-foundation:#F4F6F8;
      --txt-main:#212529;
      --txt-secondary:#6C757D;
      --act-red:#E54D42;
      --accent-yellow:#FFB900;
  }
  body{ background:var(--bg-foundation); }
  .page-title{ color:var(--txt-main); }
  .form-control{ border-radius:.5rem; padding:.65rem .9rem; }
  .btn-primary{ background:var(--act-red)!important; border-color:var(--act-red)!important; }
  .btn-primary:hover{ filter:brightness(0.95); }
  .card-soft{ border:1px solid #E9ECEF; border-radius:.75rem; box-shadow:0 4px 14px rgba(33,37,41,.06); }
  .announcement-meta{ color:var(--txt-secondary); font-size:.875rem; }
  .badge-accent{ background:var(--accent-yellow); color:var(--txt-main); }
</style>
@endsection

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold page-title mb-0">Announcements</h1>
      </div>

  {{-- Search Form --}}
  <form method="GET" class="row g-2 align-items-center mb-4">
    <div class="col-sm-10 col-12">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control"
             placeholder="ค้นหาหัวข้อหรือเนื้อหาประกาศ...">
    </div>
    <div class="col-sm-2 col-12 d-grid">
      <button class="btn btn-primary">ค้นหา</button>
    </div>
  </form>

  {{-- Announcements List --}}
  @forelse($announcements as $a)
    <div class="card card-soft mb-3">
      <div class="card-body">
        <h5 class="fw-semibold mb-1" style="color:var(--txt-main);">{{ $a->title }}</h5>
        <div class="announcement-meta mb-2">
          Audience: 
          <span class="text-capitalize">{{ $a->audience }}</span> 
          <span class="mx-2">|</span>
          Published: <span>{{ $a->published_at }}</span>
        </div>
        <div>
          <a href="{{ route('user.announcements.show', $a) }}" 
             class="text-decoration-none fw-medium"
             style="color:var(--act-red);">
             อ่านต่อ →
          </a>
        </div>
      </div>
    </div>
  @empty
    <div class="alert alert-light text-center text-secondary border rounded-4 shadow-sm">
      ยังไม่มีประกาศในขณะนี้
    </div>
  @endforelse

  {{-- Pagination --}}
  <div class="mt-4">
    {{ $announcements->links() }}
  </div>
</div>
@endsection
