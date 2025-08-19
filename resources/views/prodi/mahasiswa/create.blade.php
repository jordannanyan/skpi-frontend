@extends('adminlte::page')

@section('title', 'Tambah Mahasiswa')
@include('prodi.partials.header')

@section('content_header')
    <h1>Tambah Mahasiswa</h1>
@stop

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Form Tambah Mahasiswa</h3>
  </div>

  <form action="{{ route('prodi.mahasiswa.store') }}" method="POST" autocomplete="off">
    @csrf
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>NIM <span class="text-danger">*</span></label>
            <input type="text" name="nim_mahasiswa" class="form-control @error('nim_mahasiswa') is-invalid @enderror"
                   value="{{ old('nim_mahasiswa') }}" required>
            @error('nim_mahasiswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Nama Mahasiswa <span class="text-danger">*</span></label>
            <input type="text" name="nama_mahasiswa" class="form-control @error('nama_mahasiswa') is-invalid @enderror"
                   value="{{ old('nama_mahasiswa') }}" required>
            @error('nama_mahasiswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        {{-- Program Studi dari session (dropdown terkunci + hidden input yang dikirim) --}}
        @php
            $sessionProdiId   = session('id');           // id_prodi diset saat login role prodi
            $sessionProdiName = session('nama_prodi');   // nama_prodi diset saat login
            if (!$sessionProdiName && isset($prodiList)) {
                $row = collect($prodiList)->firstWhere('id_prodi', $sessionProdiId);
                $sessionProdiName = $row['nama_prodi'] ?? 'Program Studi';
            }
        @endphp
        <div class="col-md-4">
          <div class="form-group">
            <label>Program Studi <span class="text-danger">*</span></label>
            {{-- tetap select agar style sama, tapi disabled --}}
            <select class="form-control @error('id_prodi') is-invalid @enderror" disabled>
              <option value="">{{ $sessionProdiName ?? 'Program Studi' }}</option>
            </select>
            {{-- nilai yang dikirim ke server --}}
            <input type="hidden" name="id_prodi" value="{{ old('id_prodi', $sessionProdiId) }}">
            @error('id_prodi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Username <span class="text-danger">*</span></label>
            <input type="text" name="username" autocomplete="username"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}" required>
            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Password <span class="text-danger">*</span></label>
            <input type="password" name="password" autocomplete="new-password" minlength="6"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small class="text-muted">Minimal 6 karakter.</small>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tgl_masuk"
                   class="form-control @error('tgl_masuk') is-invalid @enderror"
                   value="{{ old('tgl_masuk') }}" required>
            @error('tgl_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Tempat Lahir <span class="text-danger">*</span></label>
            <input type="text" name="tempat_lahir"
                   class="form-control @error('tempat_lahir') is-invalid @enderror"
                   value="{{ old('tempat_lahir') }}" required>
            @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_lahir"
                   class="form-control @error('tanggal_lahir') is-invalid @enderror"
                   value="{{ old('tanggal_lahir') }}" required>
            @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>No Telp</label>
            <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                   value="{{ old('no_telp') }}">
            @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat') }}</textarea>
            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <button class="btn btn-success">Simpan</button>
      <a href="{{ route('prodi.mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
</div>
@stop
