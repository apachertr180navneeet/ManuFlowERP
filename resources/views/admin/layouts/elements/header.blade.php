<header class="main-header">
    <div class="d-flex align-items-center gap-2 gap-md-3 text-truncate">
        <button class="btn btn-link d-md-none p-1 text-secondary border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h2 class="page-title text-truncate">@yield('page_title', 'Dashboard')</h2>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <span class="badge-date d-none d-md-inline-flex">
            <i class="bi bi-calendar3 me-1 text-secondary"></i>{{ date('M d, Y') }}
        </span>
        <span class="badge-online d-none d-lg-inline-flex">
            <span class="pulse"></span>System Online
        </span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center border-0 shadow-none" type="button" data-bs-toggle="dropdown" style="padding:.25rem .375rem">
                    @if(!empty(Auth::user()->avatar) && \Illuminate\Support\Str::startsWith(Auth::user()->avatar, 'uploads/') && file_exists(public_path(Auth::user()->avatar)))
                        <img src="{{asset(Auth::user()->avatar)}}" alt="Avatar" class="rounded-circle" style="width:32px;height:32px;object-fit:cover">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:rgba(99,102,241,.15);color:#4f46e5;font-weight:700;font-size:.75rem">
                            {{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 2)) }}
                        </div>
                    @endif
                </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-radius:.75rem;min-width:180px">
                @if(Auth::user()->role == 'admin')
                <li><a class="dropdown-item py-2" href="{{route('admin.profile')}}"><i class="bi bi-person me-2 text-secondary"></i>My Profile</a></li>
                <li><a class="dropdown-item py-2" href="{{route('admin.change.password')}}"><i class="bi bi-key me-2 text-secondary"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><form action="{{route('admin.logout')}}" method="POST" class="d-inline">@csrf<button type="submit" class="dropdown-item py-2 text-danger" style="background:none;border:none;width:100%;text-align:left"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button></form></li>
                @else
                <li><a class="dropdown-item py-2" href="{{route('company.profile')}}"><i class="bi bi-person me-2 text-secondary"></i>My Profile</a></li>
                <li><a class="dropdown-item py-2" href="{{route('company.change.password')}}"><i class="bi bi-key me-2 text-secondary"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><form action="{{route('company.logout')}}" method="POST" class="d-inline">@csrf<button type="submit" class="dropdown-item py-2 text-danger" style="background:none;border:none;width:100%;text-align:left"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button></form></li>
                @endif
            </ul>
        </div>
    </div>
</header>