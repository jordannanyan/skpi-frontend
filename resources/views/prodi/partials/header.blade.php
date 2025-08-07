@section('content_top_nav_right')
    <li class="nav-item dropdown">
        <a id="userDropdown" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle"></i>
            {{ session('nama_prodi', 'User') }}
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="#">
                <i class="fas fa-user"></i> Profile
            </a>
            <div class="dropdown-divider"></div>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </li>
@endsection