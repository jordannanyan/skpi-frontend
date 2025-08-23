@extends('adminlte::page')
@section('title', 'Tambah Prodi')

@section('content_header')
    <h1>Tambah Prodi</h1>
@endsection

@section('content')
    {{-- Pesan Error API --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Pesan Success --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form --}}
    <form action="{{ route('superadmin.prodi.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Nama Prodi</label>
            <input type="text" name="nama_prodi" class="form-control" value="{{ old('nama_prodi') }}" required>
            @error('nama_prodi') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Fakultas</label>
            <select name="id_fakultas" class="form-control" required>
                <option value="">-- Pilih Fakultas --</option>
                @foreach($fakultasList as $fakultas)
                    <option value="{{ $fakultas['id_fakultas'] }}" {{ old('id_fakultas') == $fakultas['id_fakultas'] ? 'selected' : '' }}>
                        {{ $fakultas['nama_fakultas'] }}
                    </option>
                @endforeach
            </select>
            @error('id_fakultas') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
            @error('username') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Akreditasi</label>
            <input type="text" name="akreditasi" class="form-control" value="{{ old('akreditasi') }}" required>
            @error('akreditasi') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>SK Akreditasi</label>
            <input type="text" name="sk_akre" class="form-control" value="{{ old('sk_akre') }}" required>
            @error('sk_akre') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Jenis Jenjang</label>
            <input type="text" name="jenis_jenjang" class="form-control" value="{{ old('jenis_jenjang') }}" required>
            @error('jenis_jenjang') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Kompetensi Kerja</label>
            <textarea name="kompetensi_kerja" class="form-control" required>{{ old('kompetensi_kerja') }}</textarea>
            @error('kompetensi_kerja') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Bahasa</label>
            <textarea name="bahasa" class="form-control" required>{{ old('bahasa') }}</textarea>
            @error('bahasa') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Penilaian</label>
            <textarea name="penilaian" class="form-control" required>{{ old('penilaian') }}</textarea>
            @error('penilaian') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Jenis Lanjutan</label>
            <input type="text" name="jenis_lanjutan" class="form-control" value="{{ old('jenis_lanjutan') }}" required>
            @error('jenis_lanjutan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required>{{ old('alamat') }}</textarea>
            @error('alamat') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

       <button class="btn btn-success">Simpan</button>
                <a href="{{ route('superadmin.prodi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

    </form>
@endsection
