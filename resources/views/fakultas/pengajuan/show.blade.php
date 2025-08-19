@extends('adminlte::page')
@section('title', 'Detail Pengajuan')
@include('fakultas.partials.header')

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

<div class="card">
    <div class="card-body">

        <h5 class="mt-4 mb-3">Data Mahasiswa</h5>
        <table class="table table-bordered">
            <tr>
                <th>Nama</th>
                <td>{{ data_get($pengajuan, 'mahasiswa.nama_mahasiswa', '-') }}</td>
            </tr>
            <tr>
                <th>NIM</th>
                <td>{{ data_get($pengajuan, 'mahasiswa.nim_mahasiswa', '-') }}</td>
            </tr>
            <tr>
                <th>Tempat / Tanggal Lahir</th>
                <td>
                    {{ data_get($pengajuan, 'mahasiswa.tempat_lahir', '-') }},
                    {{ data_get($pengajuan, 'mahasiswa.tanggal_lahir', '-') }}
                </td>
            </tr>
            <tr>
                <th>No. Telp</th>
                <td>{{ data_get($pengajuan, 'mahasiswa.no_telp', '-') }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ data_get($pengajuan, 'mahasiswa.alamat', '-') }}</td>
            </tr>
        </table>

        <h5 class="mt-4 mb-3">Data Prodi</h5>
        <table class="table table-bordered">
            <tr>
                <th>Nama Prodi</th>
                <td>{{ data_get($prodi, 'nama_prodi', '-') }}</td>
            </tr>
            <tr>
                <th>Akreditasi</th>
                <td>{{ data_get($prodi, 'akreditasi', '-') }}</td>
            </tr>
            <tr>
                <th>Jenis/Jenjang</th>
                <td>{{ data_get($prodi, 'jenis_jenjang', '-') }}</td>
            </tr>
            <tr>
                <th>SK Akreditasi</th>
                <td>{{ data_get($prodi, 'sk_akre', '-') }}</td>
            </tr>
            <tr>
                <th>Alamat Prodi</th>
                <td>{{ data_get($prodi, 'alamat', '-') }}</td>
            </tr>
        </table>
        <br>

        <a href="{{ route('fakultas.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@stop
