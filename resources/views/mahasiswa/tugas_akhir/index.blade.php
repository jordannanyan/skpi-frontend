@extends('adminlte::page')
@section('title', 'Data Tugas Akhir')
@include('mahasiswa.partials.header')

@section('content_header')
@php
$apiBaseUrl = 'http://127.0.0.1:8000/storage/';
@endphp

<h1>Data Tugas Akhir</h1>
@stop
@section('content')
<div class="mb-3">
    <a href="{{ route('mahasiswa.tugas_akhir.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Tugas Akhir
    </a>
</div>
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Mahasiswa</th>
                        <th>Kategori</th>
                        <th>Judul</th>
                        <th>Halaman Depan</th>
                        <th>Lembar Pengesahan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            <tbody>
                @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                    <td>{{ $item['kategori'] }}</td>
                    <td>{{ $item['judul'] }}</td>
                    <td>
                        @if ($item['file_halaman_dpn'])
                        <a href="{{ $apiBaseUrl . $item['file_halaman_dpn'] }}" target="_blank">Halaman Depan</a>
                        @else
                        Tidak Ada
                        @endif
                    </td>
                    <td>
                        @if ($item['file_lembar_pengesahan'])
                        <a href="{{ $apiBaseUrl . $item['file_lembar_pengesahan'] }}" target="_blank">Lembar Pengesahan</a>
                        @else
                        Tidak Ada
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('mahasiswa.tugas_akhir.edit', $item['id_tugas_akhir']) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('mahasiswa.tugas_akhir.destroy', $item['id_tugas_akhir']) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop