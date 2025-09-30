<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Sports Field Booking')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF สำหรับ fetch/form --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ป้องกัน Alpine flash --}}
    <style>[x-cloak]{ display:none !important; }</style>

    {{-- (คงไว้ตามเดิม) --}}
    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FullCalendar v6 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.19/main.min.css" rel="stylesheet">

    <!-- สไตล์ตาม Style Guide -->
    <style>
        :root{
            --bg-foundation:#F4F6F8;
            --txt-main:#212529;
            --txt-secondary:#6C757D;
            --act-red:#E54D42;
            --accent-yellow:#FFB900;
        }
        body{ background:var(--bg-foundation); color:var(--txt-main); }
        .navbar{ background:#fff; border-bottom:1px solid #E9ECEF; }
        .navbar .nav-link{ color:var(--txt-main); }
        .navbar .nav-link:hover{ color:#000; }
        .btn-primary{
            background:var(--act-red) !important;
            border-color:var(--act-red) !important;
        }
        .btn-primary:hover{
            filter:brightness(0.95);
        }
        .badge-accent{
            background:var(--accent-yellow);
            color:var(--txt-main);
        }
        .text-secondary{ color:var(--txt-secondary) !important; }
        .shadow-soft{ box-shadow:0 4px 14px rgba(33,37,41,.06); }
        .alert-border{ border-left:4px solid var(--act-red); }
        main{ min-height: calc(100vh - 72px); } /* กันหน้าเตี้ยเกิน */
        /* กระดิ่งแจ้งเตือน */
        .bell-btn{ position:relative; background:#fff; border:1px solid #E9ECEF; }
        .bell-badge{
            position:absolute; top:-6px; right:-6px; min-width:18px; height:18px;
            font-size:11px; border-radius:999px; background:#dc3545; color:#fff;
            display:flex; align-items:center; justify-content:center; padding:0 4px;
        }
        /* Dropdown แจ้งเตือน */
        .notif-panel{
            width:22rem; background:#fff; border:1px solid #E9ECEF; border-radius:.5rem;
        }
    </style>
</head>
<body class="antialiased">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-semibold" href="{{ route('home') }}" style="color:var(--txt-main)">
            SportsBooking
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav"
                aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="topNav" class="collapse navbar-collapse">
            <!-- เมนูซ้าย -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                {{-- เมนูสำหรับ Guest --}}
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('fields.index') }}">Fields</a></li>
                @endguest

                {{-- เมนูสำหรับผู้ใช้ที่ล็อกอินแล้ว --}}
                @auth
                    @if(auth()->user()->role === 'user')
                        <li class="nav-item"><a class="nav-link" href="{{ route('bookings.index') }}">My Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('fields.index') }}">Fields</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('user.announcements.index') }}">Announcements</a></li>
                        {{-- Account ของ user จะถูกย้ายไปขวาสุดด้านล่าง (ส่วนขวา) --}}
                    @endif

                    @if(auth()->user()->role === 'staff')
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.bookings.index') }}">Requests</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.fields.index') }}">My Fields</a></li>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fields.index') }}">Fields</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.announcements.index') }}">Announcements</a></li>
                    @endif
                @endauth
            </ul>

            <!-- เมนูขวา -->
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth
                    <li class="nav-item me-2">
                        <span class="nav-link disabled text-secondary">
                            Hi, {{ auth()->user()->name }} ({{ auth()->user()->role }})
                        </span>
                    </li>

                    {{-- 🔔 กระดิ่งแจ้งเตือน (Alpine) --}}
                    <li class="nav-item dropdown me-2" x-data="notificationBell()" x-cloak x-init="init()">
                        <button class="btn bell-btn rounded-pill px-3 py-1" data-bs-toggle="dropdown" aria-expanded="false" @click="open = !open">
                            <span class="me-1">🔔</span>
                            <span class="bell-badge" x-show="unread > 0" x-text="unread"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2 notif-panel shadow-soft" x-show="open" @click.outside="open = false">
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <strong>Notifications</strong>
                                <form method="POST" action="{{ route('notifications.readAll') }}">
                                    @csrf
                                    <button class="btn btn-link btn-sm p-0 text-decoration-underline" type="submit">Mark all read</button>
                                </form>
                            </div>

                            <template x-if="items.length === 0">
                                <div class="text-secondary small px-1">No notifications</div>
                            </template>

                            <div class="list-group list-group-flush">
                                <template x-for="item in items" :key="item.id">
                                    <div class="list-group-item px-1">
                                        <div class="small" x-text="renderTitle(item)"></div>
                                        <div class="text-secondary" style="font-size:12px" x-text="new Date(item.created_at).toLocaleString()"></div>
                                        <form method="POST" :action="'/notifications/'+item.id+'/read'" class="mt-1">
                                            @csrf
                                            <button class="btn btn-link btn-sm p-0 text-decoration-underline" x-show="!item.read_at" type="submit">Mark read</button>
                                        </form>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-2 text-end px-1">
                                <a href="{{ route('notifications.index') }}" class="small text-decoration-underline">View all</a>
                            </div>
                        </div>
                    </li>

                    {{-- Account (เฉพาะ role=user) ชิดขวา --}}
                    @if(auth()->user()->role === 'user')
                        <li class="nav-item me-2">
                            <a href="{{ route('account.show') }}" class="btn btn-light border">
                                Account
                            </a>
                        </li>
                    @endif

                    {{-- ปุ่ม Logout --}}
                    <li class="nav-item">
                        <form class="d-inline" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">Logout</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-secondary" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="{{ route('register') }}">Register</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

{{-- Flash message --}}
@if(session('status'))
    <div class="container mt-3">
        <div class="alert alert-success shadow-soft border-0">
            {{ session('status') }}
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="container mt-3">
        <div class="alert alert-danger alert-border shadow-soft border-0">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<main class="container py-4">
    @yield('content')
</main>

{{-- Alpine helper สำหรับกระดิ่ง --}}
<script>
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function notificationBell() {
    return {
        open: false,
        unread: 0,
        items: [],
        fetchFeed() {
            fetch(`{{ route('notifications.feed') }}`, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    this.unread = data.unread || 0;
                    this.items = data.items || [];
                })
                .catch(() => {});
        },
        renderTitle(item) {
            if (item.type === 'booking.status.changed') {
                const s = item.data?.status ?? '';
                if (s === 'approved') return 'การจองของคุณได้รับการอนุมัติแล้ว';
                if (s === 'rejected') return 'คำขอจองของคุณถูกปฏิเสธ';
            }
            return item.data?.message ?? 'Notification';
        },
        init() {
            this.fetchFeed();
            setInterval(() => this.fetchFeed(), 60000);
        }
    }
}
</script>

{{-- Alpine fallback (ถ้าใน app.js ยังไม่ได้ start Alpine) --}}
<script>
if (typeof window.Alpine === 'undefined') {
    var s = document.createElement('script');
    s.src = 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
    s.defer = true;
    document.head.appendChild(s);
}
</script>
</body>
</html>
