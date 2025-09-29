<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Style -->
    <style>
        :root{
            --bg-foundation:#F4F6F8;
            --txt-main:#212529;
            --txt-secondary:#6C757D;
            --act-red:#E54D42;
            --accent-yellow:#FFB900;
        }
        body{
            background:var(--bg-foundation);
            font-family:'Figtree',sans-serif;
        }
        .auth-card{
            max-width:420px;
            margin:auto;
            background:#fff;
            border:1px solid #E9ECEF;
            border-radius:.75rem;
            padding:2rem;
            box-shadow:0 6px 20px rgba(33,37,41,.08);
        }
        .auth-title{
            font-weight:600;
            color:var(--txt-main);
            margin-bottom:1rem;
            text-align:center;
        }
        .form-label{
            font-weight:500;
            color:var(--txt-main);
        }
        .form-control{
            border-radius:.5rem;
            padding:.75rem 1rem;
        }
        .btn-auth{
            background:var(--act-red);
            border-color:var(--act-red);
            border-radius:.5rem;
            padding:.75rem;
            font-weight:600;
        }
        .btn-auth:hover{
            filter:brightness(0.95);
        }
        .link-accent{
            color:var(--accent-yellow);
            font-weight:500;
            text-decoration:none;
        }
        .link-accent:hover{
            text-decoration:underline;
        }
    </style>
</head>
<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="auth-card">
            {{-- content จาก Blade component --}}
            <div class="auth-title">
                @yield('auth-title','Welcome')
            </div>
            <div>
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
