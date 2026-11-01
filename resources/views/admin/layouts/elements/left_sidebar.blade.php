<aside class="sidebar d-none d-md-flex">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-boxes"></i>
        </div>
        <div class="brand-text">
            <h1>{{ config('app.name') }}</h1>
            <span>Inventory Suite</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <!-- DASHBOARD -->
            <li class="nav-item">
                <a href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('company.dashboard') }}" class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>

            <!-- MASTERS -->
            <li class="nav-header">Master</li>

            @if(Auth::user()->role == 'admin')
            <li class="nav-item">
                <a href="{{route('admin.company.index')}}" class="nav-link {{ request()->routeIs('admin.company.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Company Master
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a href="{{route('admin.vendor.index')}}" class="nav-link {{ request()->routeIs('admin.vendor.*') ? 'active' : '' }}">
                    <i class="bi bi-buildings"></i> Vendor Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.customer.index')}}" class="nav-link {{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Customer Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.salesman.index')}}" class="nav-link {{ request()->routeIs('admin.salesman.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Salesman Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.unit.index')}}" class="nav-link {{ request()->routeIs('admin.unit.*') ? 'active' : '' }}">
                    <i class="bi bi-rulers"></i> Unit Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.item.index')}}" class="nav-link {{ request()->routeIs('admin.item.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Item Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.item-assignment.index')}}" class="nav-link {{ request()->routeIs('admin.item-assignment.*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i> Item Assignment
                </a>
            </li>
            @if(Auth::user()->role !== 'admin')
            <li class="nav-item">
                <a href="{{route('company.job-worker.index')}}" class="nav-link {{ request()->routeIs('company.job-worker.*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i> Job Worker Master
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a href="{{route('admin.expense.index')}}" class="nav-link {{ request()->routeIs('admin.expense.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i> Expense Master
                </a>
            </li>

            <!-- PROCESSES -->
            <li class="nav-header">Processes</li>

            <li class="nav-item">
                <a href="{{route('admin.purchase.index')}}" class="nav-link {{ request()->routeIs('admin.purchase.*') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i> Purchase
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.manufacturing.index')}}" class="nav-link {{ request()->routeIs('admin.manufacturing.*') && !request()->routeIs('admin.manufacturing.stock') && !request()->routeIs('admin.manufacturing.item-price') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Manufacturing
                </a>
            </li>
            @if(Auth::user()->role !== 'admin')
            <li class="nav-item">
                <a href="{{route('company.job-worker-assign.index')}}" class="nav-link {{ request()->routeIs('company.job-worker-assign.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data"></i> Job Worker Assign
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('company.job-work-inward.index')}}" class="nav-link {{ request()->routeIs('company.job-work-inward.*') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down-right"></i> Job Work Inward
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a href="{{route('admin.cost-sheet.index')}}" class="nav-link {{ request()->routeIs('admin.cost-sheet.*') ? 'active' : '' }}">
                    <i class="bi bi-calculator"></i> Cost Sheet
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.dispatch.index')}}" class="nav-link {{ request()->routeIs('admin.dispatch.*') && !request()->routeIs('admin.dispatch.reports') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Dispatch
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.stock-reconciliation.index')}}" class="nav-link {{ request()->routeIs('admin.stock-reconciliation.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Stock Reconciliation
                </a>
            </li>

            <!-- REPORTS -->
            <li class="nav-header">Report</li>

            <li class="nav-item">
                <a href="{{route('admin.manufacturing.stock')}}" class="nav-link {{ request()->routeIs('admin.manufacturing.stock') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i> Stock Report
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.manufacturing.item-price')}}" class="nav-link {{ request()->routeIs('admin.manufacturing.item-price') ? 'active' : '' }}">
                    <i class="bi bi-table"></i> Item Price by Month
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.dispatch.reports')}}" class="nav-link {{ request()->routeIs('admin.dispatch.reports') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Dispatch Reports
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar-initials">{{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 2)) }}</div>
            <div class="user-text">
                <h4>{{ Auth::user()->full_name ?? Auth::user()->name ?? 'Admin' }}</h4>
                <p>{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
        <div class="d-flex gap-1">
            @if(Auth::user()->role == 'admin')
            <a href="{{route('admin.profile')}}" class="logout-btn" title="Profile"><i class="bi bi-person fs-5"></i></a>
            <form action="{{route('admin.logout')}}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="logout-btn" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></button>
            </form>
            @else
            <a href="{{route('company.profile')}}" class="logout-btn" title="Profile"><i class="bi bi-person fs-5"></i></a>
            <form action="{{route('company.logout')}}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="logout-btn" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></button>
            </form>
            @endif
        </div>
    </div>
</aside>
