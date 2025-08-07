@extends('adminlte::page')

@section('title', 'Kelola Mahasiswa')

@include('prodi.partials.header')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Kelola Mahhaha</h1>
    </div>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('prodi.mahasiswa.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Mahasiswa
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
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>No Telp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $mhs)
                        <tr>
                            <td>{{ $mhs['nim_mahasiswa'] }}</td>
                            <td>{{ $mhs['nama_mahasiswa'] }}</td>
                            <td>{{ $mhs['prodi']['nama_prodi'] ?? '-' }}</td>
                            <td>{{ $mhs['tempat_lahir'] }}</td>
                            <td>{{ $mhs['tanggal_lahir'] }}</td>
                            <td>{{ $mhs['no_telp'] }}</td>
                            <td>
                                <a href="{{ route('prodi.mahasiswa.edit', $mhs['id_mahasiswa']) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('prodi.mahasiswa.destroy', $mhs['id_mahasiswa']) }}"
                                      method="POST"
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Yakin ingin menghapus mahasiswa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Data mahasiswa tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
