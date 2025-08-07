@extends('adminlte::page')

@section('title', 'Data CPL Skor')

@include('prodi.partials.header')

@section('content_header')
    <h1>Data CPL Skor</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <div class="mb-3">
        <a href="{{ route('prodi.cpl_skor.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah CPL Skor
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama CPL</th>
                        <th>Skor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                            <td>{{ $item['cpl']['nama_cpl'] ?? '-' }}</td>
                            <td>{{ $item['skor_cpl'] }}</td>
                            <td>
                                <a href="{{ route('prodi.cpl_skor.edit', $item['id_cpl_skor']) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('prodi.cpl_skor.destroy', $item['id_cpl_skor']) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if (count($data) === 0)
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data CPL Skor</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop
