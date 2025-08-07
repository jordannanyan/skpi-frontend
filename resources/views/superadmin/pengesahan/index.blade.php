@extends('adminlte::page')

@section('title', 'Data Pengesahan')
@include('superadmin.partials.header')


@section('content_header')
<h1>Data Pengesahan</h1>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('superadmin.pengesahan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Pengesahan SKPI
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
                    <th>NIM</th>
                    <th>Fakultas</th>
                    <th>Nomor Pengesahan</th>
                    <th>Tanggal Pengesahan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['pengajuan']['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                    <td>{{ $item['pengajuan']['mahasiswa']['nim_mahasiswa'] ?? '-' }}</td>
                    <td>{{ $item['fakultas']['nama_fakultas'] ?? '-' }}</td>
                    <td>{{ $item['nomor_pengesahan'] }}</td>
                    <td>{{ $item['tgl_pengesahan'] }}</td>
                    <td>
                        <a href="{{ route('superadmin.pengesahan.edit', $item['id_pengesahan']) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('superadmin.pengesahan.destroy', $item['id_pengesahan']) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                        <a href="{{ route('superadmin.pengesahan.print', $item['id_pengesahan']) }}" class="btn btn-info btn-sm" target="_blank">Print</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop