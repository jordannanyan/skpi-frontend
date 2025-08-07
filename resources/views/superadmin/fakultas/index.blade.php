
@extends('adminlte::page')

@section('title', 'Kelola Fakultas')
@include('superadmin.partials.header')


@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Kelola Fakultas</h1>
    </div>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('superadmin.fakultas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Fakultas
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
                        <th>Nama Fakultas</th>
                        <th>Username</th>
                        <th>Nama Dekan</th>
                        <th>NIP</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $fakultas)
                        <tr>
                            <td>{{ $fakultas['nama_fakultas'] }}</td>
                            <td>{{ $fakultas['username'] }}</td>
                            <td>{{ $fakultas['nama_dekan'] }}</td>
                            <td>{{ $fakultas['nip'] }}</td>
                            <td>{{ $fakultas['alamat'] }}</td>
                            <td>
                                <a href="{{ route('superadmin.fakultas.edit', $fakultas['id_fakultas']) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('superadmin.fakultas.destroy', $fakultas['id_fakultas']) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Data fakultas tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
