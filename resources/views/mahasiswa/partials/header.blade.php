@section('content_top_nav_right')
    @php
        $role = session('role');
        $name = session('name') ?? session('nama_' . $role) ?? 'User';

        $profileRoute = match ($role) {
            'superadmin' => route('superadmin.profile.show'),
            'fakultas'   => route('fakultas.profile.show'),
            'prodi'      => route('prodi.profile.show'),
            'mahasiswa'  => route('mahasiswa.profile.show'),
            default      => '#',
        };
    @endphp

    <li class="nav-item dropdown">
        <a id="userDropdown" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle"></i>
            {{ $name }}
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="{{ $profileRoute }}">
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
