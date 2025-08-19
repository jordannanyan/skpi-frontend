@extends('adminlte::page')

@section('title', 'Detail Pengajuan')
@include('prodi.partials.header')

@section('content_header')
    <h1>Detail Pengajuan</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

@php
    // Agar fleksibel dengan nama variabel dari controller
    $p = $pengajuan ?? $item ?? [];
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Pengajuan</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th style="width: 220px;">ID Pengajuan</th>
                    <td>{{ $p['id_pengajuan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Pengajuan</th>
                    <td>{{ $p['tgl_pengajuan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat</th>
                    <td>{{ $p['created_at'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Diupdate</th>
                    <td>{{ $p['updated_at'] ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Mahasiswa</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th style="width: 220px;">NIM</th>
                    <td>{{ data_get($p, 'mahasiswa.nim_mahasiswa', '-') }}</td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td>{{ data_get($p, 'mahasiswa.nama_mahasiswa', '-') }}</td>
                </tr>
                <tr>
                    <th>Tempat, Tanggal Lahir</th>
                    <td>
                        {{ data_get($p, 'mahasiswa.tempat_lahir', '-') }},
                        {{ data_get($p, 'mahasiswa.tanggal_lahir', '-') }}
                    </td>
                </tr>
                <tr>
                    <th>No Telp</th>
                    <td>{{ data_get($p, 'mahasiswa.no_telp', '-') }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ data_get($p, 'mahasiswa.alamat', '-') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Masuk</th>
                    <td>{{ data_get($p, 'mahasiswa.tgl_masuk', '-') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Keluar</th>
                    <td>{{ data_get($p, 'mahasiswa.tgl_keluar', '-') }}</td>
                </tr>
                {{-- Keamanan: jangan tampilkan password/hash --}}
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('prodi.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@stop
