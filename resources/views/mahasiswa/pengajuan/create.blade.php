@extends('adminlte::page')
@section('title', 'Tambah Pengajuan')
@include('mahasiswa.partials.header')

@section('content_header')
    <h1>Tambah Pengajuan</h1>
@stop
@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
    <form action="{{ route('mahasiswa.pengajuan.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Mahasiswa</label>
                    @php
                        // ambil dari session mahasiswa yang login
                        $sessionMahasiswaId   = session('id');                // id_mahasiswa
                        $sessionMahasiswaName = session('nama_mahasiswa');    // nama_mahasiswa

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
                    <select name="id_kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat['id_kategori'] }}">{{ $kat['nama_kategori'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="aktif">Aktif</option>
                        <option value="noaktif">Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Pengajuan</label>
                    <input type="date" name="tgl_pengajuan" class="form-control" required>
                </div>
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('mahasiswa.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop