<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background:#0f172a;box-shadow:0 1px 3px rgba(0,0,0,.1)">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
            <div class="d-flex align-items-center justify-content-center rounded" style="width:32px;height:32px;background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;box-shadow:0 4px 12px rgba(99,102,241,.25)">
                <i class="bi bi-boxes small"></i>
            </div>
            <span>{{ config('app.name') }}</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-3" href="{{ url('/') }}" style="color:#94a3b8;font-weight:500">Home</a>
                </li>
                @auth
                    @if(Auth::user()->role == 'admin')
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-3" href="{{route('admin.dashboard')}}" style="color:#94a3b8;font-weight:500">Dashboard</a>
                    </li>
                    @endif
                    <li class="nav-item ms-lg-2">
                        <form action="{{ route('company.logout') }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-sm px-4 rounded-3 fw-semibold" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;box-shadow:0 4px 12px rgba(79,70,229,.3)">Logout</button></form>
                    </li>
                @else
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-sm px-4 rounded-3 fw-semibold" href="{{ route('admin.login') }}" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;box-shadow:0 4px 12px rgba(79,70,229,.3)">Sign In</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>