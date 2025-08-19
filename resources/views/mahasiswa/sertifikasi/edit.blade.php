@extends('adminlte::page')
@section('title', 'Edit Sertifikasi')
@include('mahasiswa.partials.header')

@section('content_header')
<h1>Edit Sertifikasi</h1>
@stop
@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form action="{{ route('mahasiswa.sertifikasi.update', $sertifikasi['id_sertifikasi']) }}?_method=PUT" method="POST" enctype="multipart/form-data">
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
                <label>Nama Sertifikasi</label>
                <input type="text" name="nama_sertifikasi" class="form-control" value="{{ $sertifikasi['nama_sertifikasi'] }}" required>
            </div>
            <div class="form-group">
                <label>Kategori Sertifikasi</label>
                @php $oldCat = strtoupper(old('kategori_sertifikasi', $sertifikasi['kategori_sertifikasi'] ?? '')); @endphp
                <select name="kategori_sertifikasi" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="KEAHLIAN" {{ $oldCat === 'KEAHLIAN' ? 'selected' : '' }}>KEAHLIAN</option>
                    <option value="PELATIHAN/SEMINAR/WORKSHOP" {{ $oldCat === 'PELATIHAN/SEMINAR/WORKSHOP' ? 'selected' : '' }}>
                        PELATIHAN/SEMINAR/WORKSHOP
                    </option>
                    <option value="PRESTASI DAN PENGHARGAAN" {{ $oldCat === 'PRESTASI DAN PENGHARGAAN' ? 'selected' : '' }}>
                        PRESTASI DAN PENGHARGAAN
                    </option>
                    <option value="PENGALAMAN ORGANISASI" {{ $oldCat === 'PENGALAMAN ORGANISASI' ? 'selected' : '' }}>
                        PENGALAMAN ORGANISASI
                    </option>
                </select>
            </div>


            <div class="form-group">
                <label>File Sertifikat</label><br>
                <small>Biarkan kosong jika tidak ingin mengubah.</small>
                <input type="file" name="file_sertifikat" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="{{ route('mahasiswa.sertifikasi.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop