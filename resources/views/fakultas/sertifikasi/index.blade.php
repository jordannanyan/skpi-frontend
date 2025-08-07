@extends('adminlte::page')
@section('title', 'Sertifikasi')
@include('fakultas.partials.header')

@section('content_header')
    <h1>Daftar Sertifikasi</h1>
@stop
@section('content')
    <div class="mb-3">
        <a href="{{ route('mahasiswa.sertifikasi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Sertifikasi
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
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Sertifikasi</th>
                        <th>Kategori</th>
                        <th>Sertifikat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $sertifikasi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sertifikasi['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                            <td>{{ $sertifikasi['nama_sertifikasi'] }}</td>
                            <td>{{ $sertifikasi['kategori_sertifikasi'] }}</td>
                            <td>
                                @if($sertifikasi['file_sertifikat'])
                                    <a href="http://127.0.0.1:8000/storage/{{ $sertifikasi['file_sertifikat'] }}" target="_blank">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('mahasiswa.sertifikasi.edit', $sertifikasi['id_sertifikasi']) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('mahasiswa.sertifikasi.destroy', $sertifikasi['id_sertifikasi']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop