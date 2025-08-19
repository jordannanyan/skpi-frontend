@extends('adminlte::page')
@section('title', 'Profil Fakultas')
@include('fakultas.partials.header')

@section('content_header')
  <h1>Profil Fakultas</h1>
@stop

@section('content')
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <div class="card">
    <form method="POST" action="{{ route('fakultas.profile.update') }}">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Fakultas</label>
            <input type="text" name="nama_fakultas" class="form-control"
                   value="{{ old('nama_fakultas', $fakultas['nama_fakultas'] ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control"
                   value="{{ old('username', $fakultas['username'] ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Password (biarkan kosong jika tidak ganti)</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
          </div>

          <div class="col-md-6">
            <label class="form-label">Nama Dekan</label>
            <input type="text" name="nama_dekan" class="form-control"
                   value="{{ old('nama_dekan', $fakultas['nama_dekan'] ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">NIP</label>
            <input type="text" name="nip" class="form-control"
                   value="{{ old('nip', $fakultas['nip'] ?? '') }}" required>
          </div>
          <div class="col-md-12">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" rows="3" class="form-control" required>{{ old('alamat', $fakultas['alamat'] ?? '') }}</textarea>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="{{ url('/fakultas/dashboard') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
@stop
