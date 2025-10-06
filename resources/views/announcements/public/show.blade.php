@extends('layouts.app')
@section('title', $announcement->title)

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
  .announcement-meta{ color:var(--txt-secondary); font-size:.9rem; }
  .btn-primary{ background:var(--act-red)!important; border-color:var(--act-red)!important; }
  .btn-primary:hover{ filter:brightness(0.95); }
  .card-soft{ border:1px solid #E9ECEF; border-radius:.75rem; box-shadow:0 4px 14px rgba(33,37,41,.06); }
</style>
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      {{-- การ์ดประกาศ --}}
      <div class="card card-soft p-4 mb-4 bg-white">
        <div class="mb-3">
          <span class="badge rounded-pill px-3 py-2" style="background:#FFB900;color:#212529;">
            Announcement
          </span>
        </div>

        <h1 class="h4 fw-semibold mb-2 page-title">{{ $announcement->title }}</h1>

        <div class="announcement-meta mb-3">
          Audience: 
          <span class="text-capitalize">{{ $announcement->audience }}</span>
          <span class="mx-2">|</span>
          Published: 
          <span>{{ $announcement->published_at }}</span>
        </div>

        <hr>

        <div class="mt-3" style="color:var(--txt-main); line-height:1.7;">
          {!! nl2br(e($announcement->content)) !!}
        </div>
      </div>

      {{-- ปุ่มกลับ --}}
      <div class="text-start">
        <a href="{{ route('user.announcements.index') }}" 
           class="btn btn-outline-secondary px-4">
          ← กลับรายการประกาศ
        </a>
      </div>

    </div>
  </div>
</div>
@endsection
