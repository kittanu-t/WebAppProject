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

    <!-- Vite (คงไว้ตามเดิม) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- THEME: สีและองค์ประกอบตาม Style Guide -->
    <style>
        :root{
            --bg-foundation:#F4F6F8; /* พื้นหลังหลัก */
            --txt-main:#212529;      /* ข้อความหลัก */
            --txt-secondary:#6C757D; /* ข้อความรอง */
            --act-red:#E54D42;       /* ปุ่มหลัก/CTA */
            --accent-yellow:#FFB900; /* badge/ลิงก์เน้น */
        }
        html,body{ height:100%; }
        body{
            background:var(--bg-foundation);
            color:var(--txt-main);
            font-family:'Figtree',sans-serif;
        }
        /* ปุ่มหลักใช้แดงตามไกด์ */
        .btn-primary{
            background:var(--act-red) !important;
            border-color:var(--act-red) !important;
        }
        .btn-primary:hover{ filter:brightness(0.95); }

        /* Card / Shadow มาตรฐาน */
        .shadow-soft{ box-shadow:0 6px 20px rgba(33,37,41,.08); }

        /* ยูทิลิตี้สำหรับหน้า auth ที่ยังอยากจัดกลางแบบเก่า (ถ้าหน้าไหนต้องการ) */
        .auth-center{
            min-height:100vh;
            display:flex; align-items:center; justify-content:center;
        }
        .auth-card{
            max-width:420px; width:100%;
            background:#fff; border:1px solid #E9ECEF; border-radius:.75rem;
            padding:2rem;
        }

        /* ฟอร์มสัมผัสนุ่มขึ้นเล็กน้อย */
        .form-control{ border-radius:.5rem; padding:.75rem 1rem; }
        .form-label{ font-weight:500; }
        .text-secondary{ color:var(--txt-secondary) !important; }
        .badge-accent{ background:var(--accent-yellow); color:var(--txt-main); }
    </style>
</head>
<body>

    {{-- ปล่อยให้หน้าลูกเป็นคนควบคุมเลย์เอาต์เอง (รองรับ 40:60 ของหน้า login ใหม่) --}}
    {{ $slot }}

</body>
</html>
