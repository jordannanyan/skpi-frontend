@extends('adminlte::page')
@section('title', 'Profil Prodi')
@include('prodi.partials.header')

@section('content_header')
  <h1>Profil Prodi</h1>
@stop

@section('content')
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

  <div class="card">
    <form method="POST" action="{{ route('prodi.profile.update') }}">
      @csrf @method('PUT')
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Prodi</label>
            <input type="text" name="nama_prodi" class="form-control"
                   value="{{ old('nama_prodi', $prodi['nama_prodi'] ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control"
                   value="{{ old('username', $prodi['username'] ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Password (opsional)</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
          </div>

          <div class="col-md-2">
            <label class="form-label">Akreditasi</label>
            <input type="text" name="akreditasi" class="form-control"
                   value="{{ old('akreditasi', $prodi['akreditasi'] ?? '') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">SK Akreditasi</label>
            <input type="text" name="sk_akre" class="form-control"
                   value="{{ old('sk_akre', $prodi['sk_akre'] ?? '') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Jenjang</label>
            <input type="text" name="jenis_jenjang" class="form-control"
                   value="{{ old('jenis_jenjang', $prodi['jenis_jenjang'] ?? '') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Kompetensi Kerja</label>
            <input type="text" name="kompetensi_kerja" class="form-control"
                   value="{{ old('kompetensi_kerja', $prodi['kompetensi_kerja'] ?? '') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Bahasa</label>
            <input type="text" name="bahasa" class="form-control"
                   value="{{ old('bahasa', $prodi['bahasa'] ?? '') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Penilaian</label>
            <input type="text" name="penilaian" class="form-control"
                   value="{{ old('penilaian', $prodi['penilaian'] ?? '') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Lanjutan</label>
            <input type="text" name="jenis_lanjutan" class="form-control"
                   value="{{ old('jenis_lanjutan', $prodi['jenis_lanjutan'] ?? '') }}">
          </div>
          <div class="col-md-12">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" rows="3" class="form-control">{{ old('alamat', $prodi['alamat'] ?? '') }}</textarea>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="{{ url('/prodi/dashboard') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
@stop
