@extends('adminlte::page')

@section('title', 'Kelola Mahasiswa')
@include('prodi.partials.header')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Kelola Mahasiswa</h1>
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
                    <th>Username</th> {{-- NEW --}}
                    <th>Prodi</th>
                    <th>Tgl Masuk</th> {{-- NEW --}}
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>No Telp</th>
                    <th style="width:180px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $mhs)
                <tr>
                    <td>{{ $mhs['nim_mahasiswa'] ?? '-' }}</td>
                    <td>{{ $mhs['nama_mahasiswa'] ?? '-' }}</td>
                    <td>{{ $mhs['username'] ?? '-' }}</td> {{-- NEW --}}

                    {{-- Ambil nama prodi kalau disediakan nested; fallback ke id_prodi --}}
                    <td>{{ data_get($mhs, 'prodi.nama_prodi', $mhs['id_prodi'] ?? '-') }}</td>

                    {{-- Format tgl_masuk aman --}}
                    <td>
                        @php $tm = $mhs['tgl_masuk'] ?? null; @endphp
                        {{ $tm ? \Carbon\Carbon::parse($tm)->format('d/m/Y') : '-' }}
                    </td>

                    <td>{{ $mhs['tempat_lahir'] ?? '-' }}</td>
                    <td>
                        @php $tl = $mhs['tanggal_lahir'] ?? null; @endphp
                        {{ $tl ? \Carbon\Carbon::parse($tl)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $mhs['no_telp'] ?? '-' }}</td>

                    <td class="text-nowrap">
                        <a href="{{ route('prodi.mahasiswa.edit', $mhs['id_mahasiswa']) }}"
                            class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('prodi.mahasiswa.destroy', $mhs['id_mahasiswa']) }}"
                            method="POST" class="d-inline"
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
                    <td colspan="9" class="text-center text-muted">Data mahasiswa tidak ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop