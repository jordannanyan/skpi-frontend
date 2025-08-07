@extends('adminlte::page')
@section('title', 'Data Isi Capaian')
@include('superadmin.partials.header')

@section('content_header')
    <h1>Data Isi Capaian</h1>
@stop
@section('content')
    <div class="mb-3">
        <a href="{{ route('superadmin.isi_capaian.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Isi Capaian
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
                            <th>Mahasiswa</th>
                            <th>Deskripsi (ID)</th>
                            <th>Deskripsi (EN)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['cpl_skor']['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                                <td>{{ $item['deskripsi_indo'] }}</td>
                                <td>{{ $item['deskripsi_inggris'] }}</td>
                                <td>
                                    <a href="{{ route('superadmin.isi_capaian.edit', $item['id_capaian']) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('superadmin.isi_capaian.destroy', $item['id_capaian']) }}" method="POST" style="display:inline-block">
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
    </div>
@stop