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

    <form action="{{ route('prodi.mahasiswa.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim_mahasiswa" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama_mahasiswa" class="form-control" required>
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
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" required>
            </div>

            <div class="form-group">
                <label>No Telp</label>
                <input type="text" name="no_telp" class="form-control">
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <div class="card-footer">
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('prodi.mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@stop