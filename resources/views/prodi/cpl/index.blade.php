@extends('adminlte::page')

@section('title', 'Kelola CPL')
@include('prodi.partials.header')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Kelola CPL</h1>
    </div>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('prodi.cpl.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah CPL
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
                        <th>Nama CPL</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $cpl)
                        <tr>
                            <td>{{ $cpl['nama_cpl'] }}</td>
                            <td>
                                <span class="badge bg-{{ $cpl['status'] === 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($cpl['status']) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('prodi.cpl.edit', $cpl['id_cpl']) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('prodi.cpl.destroy', $cpl['id_cpl']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Data CPL tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
