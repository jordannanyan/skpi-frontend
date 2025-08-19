@extends('adminlte::page')

@section('title', 'Edit Mahasiswa')

@include('prodi.partials.header')

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
        <form action="{{ route('prodi.mahasiswa.update', $mahasiswa['id_mahasiswa']) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nim_mahasiswa">NIM</label>
                <input type="text" name="nim_mahasiswa" class="form-control" value="{{ old('nim_mahasiswa', $mahasiswa['nim_mahasiswa']) }}" required>
            </div>

            <div class="form-group">
                <label for="nama_mahasiswa">Nama</label>
                <input type="text" name="nama_mahasiswa" class="form-control" value="{{ old('nama_mahasiswa', $mahasiswa['nama_mahasiswa']) }}" required>
            </div>

            <div class="form-group">
                <label>Program Studi</label>
                @php
                $sessionProdiId = session('id'); // id prodi yang login
                $sessionProdiName = session('nama_prodi'); // nama prodi di session (kalau ada)

                // fallback: cari nama di $prodiList jika belum ada di session
                if (!$sessionProdiName && isset($prodiList)) {
                $row = collect($prodiList)->firstWhere('id_prodi', $sessionProdiId);
                $sessionProdiName = $row['nama_prodi'] ?? 'Program Studi';
                }
                @endphp

                {{-- tampilkan seperti dropdown tapi dikunci --}}
                <select class="form-control" disabled>
                    <option value="">{{ $sessionProdiName ?? 'Program Studi' }}</option>
                </select>

                {{-- nilai yang benar-benar dikirim ke server --}}
                <input type="hidden" name="id_prodi" value="{{ old('id_prodi', $sessionProdiId) }}">
            </div>


            <div class="form-group">
                <label for="tempat_lahir">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $mahasiswa['tempat_lahir']) }}" required>
            </div>

            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $mahasiswa['tanggal_lahir']) }}" required>
            </div>

            <div class="form-group">
                <label for="no_telp">No Telp</label>
                <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp', $mahasiswa['no_telp']) }}">
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" class="form-control">{{ old('alamat', $mahasiswa['alamat']) }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('prodi.mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@stop