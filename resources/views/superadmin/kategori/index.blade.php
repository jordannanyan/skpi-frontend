@extends('adminlte::page')
@section('title', 'Data Kategori')
@include('superadmin.partials.header')

@section('content_header')
    <h1>Data Kategori</h1>
@stop
@section('content')
    <div class="mb-3">
        <a href="{{ route('superadmin.kategori.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah CPL Skor
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
                        <th>Nama Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['nama_kategori'] }}</td>
                            <td>{{ $item['status'] }}</td>
                            <td>
                                <a href="{{ route('superadmin.kategori.edit', $item['id_kategori']) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('superadmin.kategori.destroy', $item['id_kategori']) }}" method="POST" style="display:inline-block">
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