<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/x-icon" href="{{asset('assets/admin/img/favicon/favicon.ico')}}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('style')
</head>
<body>
    <div class="login-split">
        <div class="login-brand-panel">
            <div class="brand-panel-content">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="d-flex align-items-center justify-content-center rounded" style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;box-shadow:0 4px 12px rgba(99,102,241,.25)">
                        <i class="bi bi-boxes fs-5"></i>
                    </div>
                    <div>
                        <h2 class="fs-5 fw-bold text-white mb-0">{{ config('app.name') }}</h2>
                    </div>
                </div>
                <span class="brand-tag">Enterprise Resource Planning Suite</span>
                <h2>Manage your entire operations in one central hub.</h2>
                <p>Access premium analytics modules, secure databases, user management portals, and unified auditing ledger flows instantly.</p>
            </div>
            <div class="brand-footer">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
                <span class="online-indicator"><span class="dot"></span>System Online</span>
            </div>
        </div>
        <div class="login-form-panel">
            <div class="login-card">
                @yield('content')
            </div>
        </div>
    </div>
    @include('admin.layouts.elements.sweet_alerts')
    @yield('script')
</body>
</html>