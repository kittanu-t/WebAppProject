<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Sports Field Booking')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="antialiased">
    <header class="p-4 border-b">
        <a href="{{ route('home') }}">Home</a>
        @auth
            <span class="ml-2">Hi, {{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <form class="inline ml-2" method="POST" action="{{ route('logout') }}">@csrf<button>Logout</button></form>
        @endauth
        @guest
            <a class="ml-2" href="{{ route('login') }}">Login</a>
            <a class="ml-2" href="{{ route('register') }}">Register</a>
        @endguest
    </header>
    <main class="p-6">@yield('content')</main>
</body>
</html>
