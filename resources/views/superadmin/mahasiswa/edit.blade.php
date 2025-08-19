@extends('adminlte::page')

@section('title', 'Edit Mahasiswa')
@include('superadmin.partials.header')

@section('content_header')
    <h1>Edit Mahasiswa</h1>
@stop

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card">
  <div class="card-body">
    <form action="{{ route('superadmin.mahasiswa.update', $mahasiswa['id_mahasiswa']) }}" method="POST" autocomplete="off">
      @csrf
      @method('PUT')

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="nim_mahasiswa">NIM <span class="text-danger">*</span></label>
            <input type="text" name="nim_mahasiswa" id="nim_mahasiswa"
                   class="form-control @error('nim_mahasiswa') is-invalid @enderror"
                   value="{{ old('nim_mahasiswa', $mahasiswa['nim_mahasiswa'] ?? '') }}" required>
            @error('nim_mahasiswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="nama_mahasiswa">Nama <span class="text-danger">*</span></label>
            <input type="text" name="nama_mahasiswa" id="nama_mahasiswa"
                   class="form-control @error('nama_mahasiswa') is-invalid @enderror"
                   value="{{ old('nama_mahasiswa', $mahasiswa['nama_mahasiswa'] ?? '') }}" required>
            @error('nama_mahasiswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="id_prodi">Program Studi <span class="text-danger">*</span></label>
            <select name="id_prodi" id="id_prodi"
                    class="form-control @error('id_prodi') is-invalid @enderror" required>
              <option value="">-- Pilih Prodi --</option>
              @foreach($prodiList as $prodi)
                <option value="{{ $prodi['id_prodi'] }}"
                  {{ old('id_prodi', $mahasiswa['id_prodi'] ?? null) == $prodi['id_prodi'] ? 'selected' : '' }}>
                  {{ $prodi['nama_prodi'] }}
                </option>
              @endforeach
            </select>
            @error('id_prodi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Username <span class="text-danger">*</span></label>
            <input type="text" name="username" autocomplete="username"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $mahasiswa['username'] ?? '') }}" required>
            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Password (biarkan kosong jika tidak diganti)</label>
            <input type="password" name="password" autocomplete="new-password" minlength="6"
                   class="form-control @error('password') is-invalid @enderror">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tgl_masuk"
                   class="form-control @error('tgl_masuk') is-invalid @enderror"
                   value="{{ old('tgl_masuk', $mahasiswa['tgl_masuk'] ?? '') }}" required>
            @error('tgl_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
            <input type="text" name="tempat_lahir" id="tempat_lahir"
                   class="form-control @error('tempat_lahir') is-invalid @enderror"
                   value="{{ old('tempat_lahir', $mahasiswa['tempat_lahir'] ?? '') }}" required>
            @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                   class="form-control @error('tanggal_lahir') is-invalid @enderror"
                   value="{{ old('tanggal_lahir', $mahasiswa['tanggal_lahir'] ?? '') }}" required>
            @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="no_telp">No Telp</label>
            <input type="text" name="no_telp" id="no_telp"
                   class="form-control @error('no_telp') is-invalid @enderror"
                   value="{{ old('no_telp', $mahasiswa['no_telp'] ?? '') }}">
            @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea name="alamat" id="alamat"
                      class="form-control @error('alamat') is-invalid @enderror"
                      rows="3">{{ old('alamat', $mahasiswa['alamat'] ?? '') }}</textarea>
            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Simpan Perubahan
      </button>
      <a href="{{ route('superadmin.mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>
@stop
