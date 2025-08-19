@extends('adminlte::page')

@section('title', 'Data Pengajuan')

@include('prodi.partials.header')

@section('content_header')
<h1>Data Pengajuan</h1>
@stop

@section('content')
<div class="mb-3">
    <a href="{{ route('prodi.pengajuan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Pengajuan SKPI
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
                    <th>#</th>
                    <th>Nama Mahasiswa</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                    <td>{{ $item['tgl_pengajuan'] }}</td>
                    <td>
                        <a href="{{ route('prodi.pengajuan.show', $item['id_pengajuan']) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Detail
                        </a>

                        <a href="{{ route('prodi.pengajuan.edit', $item['id_pengajuan']) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <form action="{{ route('prodi.pengajuan.destroy', $item['id_pengajuan']) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop