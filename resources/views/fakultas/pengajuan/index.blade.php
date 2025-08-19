@extends('adminlte::page')

@section('title', 'Data Pengajuan')

@include('fakultas.partials.header')


@section('content_header')
<h1>Data Pengajuan</h1>
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
                        <a href="{{ route('fakultas.pengajuan.show', $item['id_pengajuan']) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop