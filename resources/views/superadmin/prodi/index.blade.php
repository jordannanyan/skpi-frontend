@extends('adminlte::page')

@section('title', 'Kelola Prodi')
@include('superadmin.partials.header')


@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Kelola Program Studi</h1>
    </div>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('superadmin.prodi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Prodi
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
                        <th>Nama Prodi</th>
                        <th>Fakultas</th>
                        <th>Akreditasi</th>
                        <th>Jenjang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $prodi)
                        <tr>
                            <td>{{ $prodi['nama_prodi'] }}</td>
                            <td>{{ $prodi['fakultas']['nama_fakultas'] ?? '-' }}</td>
                            <td>{{ $prodi['akreditasi'] }}</td>
                            <td>{{ $prodi['jenis_jenjang'] }}</td>
                            <td>
                                <a href="{{ route('superadmin.prodi.edit', $prodi['id_prodi']) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('superadmin.prodi.destroy', $prodi['id_prodi']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Data prodi tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop