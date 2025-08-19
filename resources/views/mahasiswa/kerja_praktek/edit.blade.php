@extends('adminlte::page')
@section('title', 'Edit Kerja Praktek')
@include('mahasiswa.partials.header')

@section('content_header')
<h1>Edit Kerja Praktek</h1>
@stop
@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form action="{{ route('mahasiswa.kerja_praktek.update', $kerja_praktek['id_kerja_praktek']) }}?_method=PUT" method="POST" enctype="multipart/form-data">
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
                <label>Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" class="form-control" value="{{ $kerja_praktek['nama_kegiatan'] }}" required>
            </div>
            <div class="form-group">
                <label>File Sertifikat</label><br>
                <small>Biarkan kosong jika tidak ingin mengubah.</small>
                <input type="file" name="file_sertifikat" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="{{ route('mahasiswa.kerja_praktek.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop