@extends('adminlte::page')
@section('title', 'Profil Admin')
@include('superadmin.partials.header')

@section('content_header')
  <h1>Profil Admin</h1>
@stop

@section('content')
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

  <div class="card">
    <form method="POST" action="{{ route('superadmin.profile.update') }}">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control"
                   value="{{ old('username', $admin['username'] ?? '') }}" required autocomplete="username">
          </div>

          {{-- Bagian ubah password, opsional --}}
          <div class="col-md-6">
            <label class="form-label">Password baru <small class="text-muted">(biarkan kosong jika tidak diganti)</small></label>
            <input type="password" name="password" class="form-control"
                   autocomplete="new-password" id="password_new">
          </div>

          <div class="col-md-6">
            <label class="form-label">Konfirmasi password baru</label>
            <input type="password" name="password_confirmation" class="form-control"
                   autocomplete="new-password" id="password_confirm">
          </div>

          {{-- WAJIB diisi kalau mau ganti password --}}
          <div class="col-md-6">
            <label class="form-label">Password saat ini <small class="text-muted">(wajib bila mengganti password)</small></label>
            <input type="password" name="current_password" class="form-control"
                   autocomplete="current-password" id="password_current">
          </div>
        </div>
      </div>

      <div class="card-footer">
        <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="{{ url('/superadmin/dashboard') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
@stop

@push('js')
<script>
  // Client-side hint: kalau password baru diisi, wajibkan current_password & konfirmasi
  const newPwd = document.getElementById('password_new');
  const curPwd = document.getElementById('password_current');
  const confPwd = document.getElementById('password_confirm');

  function toggleRequirements() {
    const required = newPwd.value.length > 0;
    curPwd.required = required;
    confPwd.required = required;
  }

  newPwd.addEventListener('input', toggleRequirements);
  document.addEventListener('DOMContentLoaded', toggleRequirements);
</script>
@endpush
