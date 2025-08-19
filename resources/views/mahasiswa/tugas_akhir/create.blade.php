@extends('adminlte::page')
@section('title', 'Tambah Tugas Akhir')
@include('mahasiswa.partials.header')

@section('content_header')
<h1>Tambah Tugas Akhir</h1>
@stop
@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form action="{{ route('mahasiswa.tugas_akhir.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Mahasiswa</label>
                @php
                // ambil dari session mahasiswa yang login
                $sessionMahasiswaId = session('id'); // id_mahasiswa
                $sessionMahasiswaName = session('nama_mahasiswa'); // nama_mahasiswa

                // fallback: cari nama di koleksi $mahasiswa jika belum ada di session
                if (!$sessionMahasiswaName && isset($mahasiswa)) {
                $row = collect($mahasiswa)->firstWhere('id_mahasiswa', $sessionMahasiswaId);
                $sessionMahasiswaName = $row['nama_mahasiswa'] ?? 'Mahasiswa';
                }
                @endphp

                {{-- tampilkan dropdown terkunci agar gaya tetap sama --}}
                <select class="form-control" disabled>
                    <option value="">{{ $sessionMahasiswaName ?? 'Mahasiswa' }}</option>
                </select>

                {{-- nilai yang dikirim ke server --}}
                <input type="hidden" name="id_mahasiswa" value="{{ old('id_mahasiswa', $sessionMahasiswaId) }}">
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Skripsi" {{ old('kategori') === 'Skripsi' ? 'selected' : '' }}>Skripsi</option>
                    <option value="Tesis" {{ old('kategori') === 'Tesis' ? 'selected' : '' }}>Tesis</option>
                    <option value="Disertasi" {{ old('kategori') === 'Disertasi' ? 'selected' : '' }}>Disertasi</option>
                </select>
            </div>
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Halaman Depan</label>
                <input type="file" name="file_halaman_dpn" class="form-control">
            </div>
            <div class="form-group">
                <label>Lembar Pengesahan</label>
                <input type="file" name="file_lembar_pengesahan" class="form-control">
            </div>
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('mahasiswa.tugas_akhir.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop