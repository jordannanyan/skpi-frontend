@extends('adminlte::page')
@section('title', 'Tambah Pengesahan')
@include('fakultas.partials.header')

@section('content_header')
<h1>Tambah Pengesahan</h1>
@stop
@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form action="{{ route('fakultas.pengesahan.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Fakultas</label>
                @php
                $sessionFakultasId = session('id');
                $sessionFakultasName = session('nama_fakultas');
                // fallback cari nama dari koleksi $fakultas jika belum ada di session
                if (!$sessionFakultasName && isset($fakultas)) {
                $row = collect($fakultas)->firstWhere('id_fakultas', $sessionFakultasId);
                $sessionFakultasName = $row['nama_fakultas'] ?? 'Fakultas';
                }
                @endphp

                {{-- Dropdown dikunci agar tampilan tetap sama --}}
                <select class="form-control" disabled>
                    <option value="">{{ $sessionFakultasName ?? 'Fakultas' }}</option>
                </select>

                {{-- Nilai yang benar-benar dikirim ke server --}}
                <input type="hidden" name="id_fakultas" value="{{ old('id_fakultas', $sessionFakultasId) }}">
            </div>

            <div class="form-group">
                <label>Pengajuan</label>
                <select name="id_pengajuan" class="form-control" required>
                    <option value="">-- Pilih Pengajuan --</option>
                    @foreach($pengajuan as $item)
                    <option value="{{ $item['id_pengajuan'] }}">{{ $item['mahasiswa']['nama_mahasiswa'] ?? '-' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Nomor Pengesahan</label>
                <input type="text" name="nomor_pengesahan" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Tanggal Pengesahan</label>
                <input type="date" name="tgl_pengesahan" class="form-control" required>
            </div>
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('fakultas.pengesahan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop