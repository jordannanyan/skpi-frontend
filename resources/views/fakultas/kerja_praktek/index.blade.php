@extends('adminlte::page')
@section('title', 'Kerja Praktek')

@include('fakultas.partials.header')

@section('content_header')
    <h1>Daftar Kerja Praktek</h1>
@stop
@section('content')
    <div class="mb-3">
        <a href="{{ route('mahasiswa.kerja_praktek.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Kerja Praktek
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
                        <th>Nama Kegiatan</th>
                        <th>Sertifikat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $kp)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $kp['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                            <td>{{ $kp['nama_kegiatan'] }}</td>
                            <td>
                                @if($kp['file_sertifikat'])
                                    <a href="http://127.0.0.1:8000/storage/{{ $kp['file_sertifikat'] }}" target="_blank">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('mahasiswa.kerja_praktek.edit', $kp['id_kerja_praktek']) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('mahasiswa.kerja_praktek.destroy', $kp['id_kerja_praktek']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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