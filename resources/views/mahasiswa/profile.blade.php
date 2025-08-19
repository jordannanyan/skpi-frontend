@extends('adminlte::page')
@section('title', 'Profil Mahasiswa')
@include('mahasiswa.partials.header')

@section('content_header')
  <h1>Profil Mahasiswa</h1>
@stop

@section('content')
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

  <div class="card">
    <form method="POST" action="{{ route('mahasiswa.profile.update') }}">
      @csrf @method('PUT')
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">NIM</label>
            <input type="text" name="nim_mahasiswa" class="form-control"
                   value="{{ old('nim_mahasiswa', $mahasiswa['nim_mahasiswa'] ?? '') }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Nama</label>
            <input type="text" name="nama_mahasiswa" class="form-control"
                   value="{{ old('nama_mahasiswa', $mahasiswa['nama_mahasiswa'] ?? '') }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control"
                   value="{{ old('username', $mahasiswa['username'] ?? '') }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Password (opsional)</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
          </div>
          <div class="col-md-4">
            <label class="form-label">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control"
                   value="{{ old('tempat_lahir', $mahasiswa['tempat_lahir'] ?? '') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control"
                   value="{{ old('tanggal_lahir', $mahasiswa['tanggal_lahir'] ?? '') }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">No. Telp</label>
            <input type="text" name="no_telp" class="form-control"
                   value="{{ old('no_telp', $mahasiswa['no_telp'] ?? '') }}">
          </div>
          <div class="col-md-8">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" class="form-control"
                   value="{{ old('alamat', $mahasiswa['alamat'] ?? '') }}">
          </div>

          <div class="col-12">
            <small class="text-muted">Program studi & fakultas mengikuti data sistem, tidak bisa diubah dari profil.</small>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="{{ url('/mahasiswa/dashboard') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
@stop
