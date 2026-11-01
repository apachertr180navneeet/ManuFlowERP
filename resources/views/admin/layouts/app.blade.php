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
    <div class="d-flex">
        @include('admin.layouts.elements.left_sidebar')
        <div class="main-wrapper">
            @include('admin.layouts.elements.header')
            <main class="main-content">
                @yield('content')
            </main>
            @include('admin.layouts.elements.footer')
        </div>
    </div>

    <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="sidebarOffcanvas" style="background:#0f172a!important">
        <div class="offcanvas-header border-bottom border-secondary">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-icon d-flex align-items-center justify-content-center rounded" style="width:32px;height:32px;background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;box-shadow:0 4px 12px rgba(99,102,241,.25)">
                    <i class="bi bi-boxes"></i>
                </div>
                <div>
                    <h1 class="fs-6 fw-bold text-white mb-0">{{ config('app.name') }}</h1>
                    <span class="text-secondary" style="font-size:.625rem;text-transform:uppercase;letter-spacing:.1em">Inventory Suite</span>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-3">
            <ul class="nav flex-column">
                <!-- DASHBOARD -->
                <li class="nav-item mb-1">
                    <a href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('company.dashboard') }}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('*.dashboard') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-grid-1x2 me-3 fs-5" style="color:#64748b"></i> Dashboard
                    </a>
                </li>

                <!-- MASTERS -->
                <li class="nav-header text-uppercase text-secondary fw-bold px-3 pt-3 pb-1" style="font-size: .6875rem; letter-spacing: .08em; color: #64748b !important; list-style: none;">Master</li>

                @if(Auth::user()->role == 'admin')
                <li class="nav-item mb-1">
                    <a href="{{route('admin.company.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.company.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-building me-3 fs-5" style="color:#64748b"></i> Company Master
                    </a>
                </li>
                @endif

                <li class="nav-item mb-1">
                    <a href="{{route('admin.vendor.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.vendor.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-buildings me-3 fs-5" style="color:#64748b"></i> Vendor Master
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.customer.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.customer.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-people me-3 fs-5" style="color:#64748b"></i> Customer Master
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.salesman.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.salesman.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-person-badge me-3 fs-5" style="color:#64748b"></i> Salesman Master
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.unit.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.unit.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-rulers me-3 fs-5" style="color:#64748b"></i> Unit Master
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.item.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.item.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-box-seam me-3 fs-5" style="color:#64748b"></i> Item Master
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.item-assignment.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.item-assignment.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-link-45deg me-3 fs-5" style="color:#64748b"></i> Item Assignment
                    </a>
                </li>
                @if(Auth::user()->role !== 'admin')
                <li class="nav-item mb-1">
                    <a href="{{route('company.job-worker.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('company.job-worker.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-person-workspace me-3 fs-5" style="color:#64748b"></i> Job Worker Master
                    </a>
                </li>
                @endif
                <li class="nav-item mb-1">
                    <a href="{{route('admin.expense.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.expense.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-cash-coin me-3 fs-5" style="color:#64748b"></i> Expense Master
                    </a>
                </li>

                <!-- PROCESSES -->
                <li class="nav-header text-uppercase text-secondary fw-bold px-3 pt-3 pb-1" style="font-size: .6875rem; letter-spacing: .08em; color: #64748b !important; list-style: none;">Processes</li>

                <li class="nav-item mb-1">
                    <a href="{{route('admin.purchase.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.purchase.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-cart3 me-3 fs-5" style="color:#64748b"></i> Purchase
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.manufacturing.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.manufacturing.*') && !request()->routeIs('admin.manufacturing.stock') && !request()->routeIs('admin.manufacturing.item-price') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-gear me-3 fs-5" style="color:#64748b"></i> Manufacturing
                    </a>
                </li>
                @if(Auth::user()->role !== 'admin')
                <li class="nav-item mb-1">
                    <a href="{{route('company.job-worker-assign.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('company.job-worker-assign.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-clipboard-data me-3 fs-5" style="color:#64748b"></i> Job Worker Assign
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('company.job-work-inward.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('company.job-work-inward.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-box-arrow-in-down-right me-3 fs-5" style="color:#64748b"></i> Job Work Inward
                    </a>
                </li>
                @endif
                <li class="nav-item mb-1">
                    <a href="{{route('admin.cost-sheet.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.cost-sheet.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-calculator me-3 fs-5" style="color:#64748b"></i> Cost Sheet
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.dispatch.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.dispatch.*') && !request()->routeIs('admin.dispatch.reports') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-truck me-3 fs-5" style="color:#64748b"></i> Dispatch
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.stock-reconciliation.index')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.stock-reconciliation.*') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-clipboard-check me-3 fs-5" style="color:#64748b"></i> Stock Reconciliation
                    </a>
                </li>

                <!-- REPORTS -->
                <li class="nav-header text-uppercase text-secondary fw-bold px-3 pt-3 pb-1" style="font-size: .6875rem; letter-spacing: .08em; color: #64748b !important; list-style: none;">Report</li>

                <li class="nav-item mb-1">
                    <a href="{{route('admin.manufacturing.stock')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.manufacturing.stock') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-boxes me-3 fs-5" style="color:#64748b"></i> Stock Report
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.manufacturing.item-price')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.manufacturing.item-price') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-table me-3 fs-5" style="color:#64748b"></i> Item Price by Month
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{route('admin.dispatch.reports')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.dispatch.reports') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-file-earmark-bar-graph me-3 fs-5" style="color:#64748b"></i> Dispatch Reports
                    </a>
                </li>
            </ul>
        </div>
        <div class="p-3 border-top border-secondary">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:rgba(99,102,241,.2);color:#a5b4fc;font-weight:700;font-size:.875rem">
                    {{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 2)) }}
                </div>
                <div class="text-truncate">
                    <h6 class="text-white mb-0" style="font-size:.75rem">{{ Auth::user()->full_name ?? 'Admin' }}</h6>
                    <small class="text-secondary" style="font-size:.625rem">{{ Auth::user()->email ?? '' }}</small>
                </div>
            </div>
        </div>
    </div>

    <script>const BASE_URL = '{{ url('/') }}';</script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('admin.layouts.elements.sweet_alerts')
    @yield('script')
</body>
</html>
