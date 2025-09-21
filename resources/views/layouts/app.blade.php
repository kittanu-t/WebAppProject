<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Sports Field Booking')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF สำหรับ fetch/form --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="antialiased">
<header class="p-4 border-b flex items-center justify-between">
    <div class="space-x-3">
        <a href="{{ route('home') }}">Home</a>

        @auth
            {{-- เมนูตามบทบาท (ตัวอย่างลิงก์หลัก) --}}
            @if(auth()->user()->role === 'user')
                <a href="{{ route('bookings.index') }}">My Bookings</a>
            @endif

            @if(auth()->user()->role === 'staff')
                <a href="{{ route('staff.bookings.index') }}">Requests</a>
                <a href="{{ route('staff.fields.schedule') }}">My Fields Schedule</a>
            @endif

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Admin</a>
                <a href="{{ route('admin.users.index') }}">Users</a>
                <a href="{{ route('admin.fields.index') }}">Fields</a>
                <a href="{{ route('admin.announcements.index') }}">Announcements</a>
            @endif
        @endauth
    </div>

    <div class="flex items-center space-x-3">
        @auth
            <span>Hi, {{ auth()->user()->name }} ({{ auth()->user()->role }})</span>

            {{-- 🔔 กระดิ่งแจ้งเตือน --}}
            <div x-data="notificationBell()" x-init="init()" class="relative inline-block">
                <button @click="open = !open" class="relative">
                    🔔
                    <span x-show="unread > 0"
                          x-text="unread"
                          class="absolute -top-2 -right-2 text-xs bg-red-600 text-white rounded-full px-1"></span>
                </button>

                <div x-show="open" @click.outside="open = false"
                     class="absolute right-0 mt-2 w-80 bg-white border shadow p-2 z-50">
                    <div class="flex justify-between items-center mb-2">
                        <strong>Notifications</strong>
                        <form method="POST" action="{{ route('notifications.readAll') }}">
                            @csrf
                            <button class="text-sm underline">Mark all read</button>
                        </form>
                    </div>

                    <template x-if="items.length === 0">
                        <div class="text-sm text-gray-500">No notifications</div>
                    </template>

                    <template x-for="item in items" :key="item.id">
                        <div class="border-b py-2 text-sm">
                            <div x-text="renderTitle(item)"></div>
                            <div class="text-xs text-gray-500" x-text="new Date(item.created_at).toLocaleString()"></div>
                            <form method="POST" :action="'/notifications/'+item.id+'/read'" class="mt-1">
                                @csrf
                                <button class="text-xs underline" x-show="!item.read_at">Mark read</button>
                            </form>
                        </div>
                    </template>

                    <div class="mt-2 text-right">
                        <a href="{{ route('notifications.index') }}" class="text-sm underline">View all</a>
                    </div>
                </div>
            </div>

            <form class="inline ml-2" method="POST" action="{{ route('logout') }}">
                @csrf
                <button>Logout</button>
            </form>
        @endauth

        @guest
            <a class="ml-2" href="{{ route('login') }}">Login</a>
            <a class="ml-2" href="{{ route('register') }}">Register</a>
        @endguest
    </div>
</header>

{{-- Flash message ง่าย ๆ --}}
@if(session('status'))
    <div class="p-3 bg-green-100 text-green-800 border-b">
        {{ session('status') }}
    </div>
@endif
@if ($errors->any())
    <div class="p-3 bg-red-100 text-red-800 border-b">
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<main class="p-6">@yield('content')</main>

{{-- Alpine helper สำหรับกระดิ่ง (ใช้งานกับ Laravel 9 + Jetstream/Alpine) --}}
<script>
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
            setInterval(() => this.fetchFeed(), 60000); // refresh ทุก 60 วิ
        }
    }
}
</script>
</body>
</html>
