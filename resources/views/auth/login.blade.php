@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])
@section('adminlte_css')
    <style>
        .login-logo {
            display: none !important;
        }
    </style>
@endsection

@section('login_logo')
    {{-- Empty section to override AdminLTE default --}}
@endsection

@section('auth_header')
    <div class="text-center">
        <img src="{{ asset('images/upr_logo.png') }}" alt="Logo UPR" width="80" class="mb-3">
        <h4>Universitas Palangka Raya</h4>
    </div>
@endsection

@section('auth_body')
    {{-- Display validation or login error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first('login') ?? $errors->first() }}
        </div>
    @endif

    <form action="{{ route('custom.login') }}" method="POST">
        @csrf

        {{-- Username --}}
        <div class="input-group mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus value="{{ old('username') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        {{-- Role --}}
        <div class="form-group mb-3">
            <select name="role" class="form-control" required>
                <option value="">-- Pilih Role --</option>
                <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                <option value="fakultas" {{ old('role') == 'fakultas' ? 'selected' : '' }}>Fakultas</option>
                <option value="prodi" {{ old('role') == 'prodi' ? 'selected' : '' }}>Prodi</option>
                <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
            </select>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0 text-center">
        &copy; {{ date('Y') }} SKPI Sistem | Universitas Palangka Raya
    </p>
@endsection
