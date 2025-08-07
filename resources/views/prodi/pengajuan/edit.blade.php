@extends('adminlte::page')
@section('title', 'Edit Pengajuan')
@include('prodi.partials.header')

@section('content_header')
    <h1>Edit Pengajuan</h1>
@stop
@section('content')
    <form action="{{ route('prodi.pengajuan.update', $pengajuan['id_pengajuan']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Mahasiswa</label>
                    <select name="id_mahasiswa" class="form-control" required>
                        @foreach($mahasiswa as $mhs)
                            <option value="{{ $mhs['id_mahasiswa'] }}" {{ $pengajuan['id_mahasiswa'] == $mhs['id_mahasiswa'] ? 'selected' : '' }}>{{ $mhs['nama_mahasiswa'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-control" required>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat['id_kategori'] }}" {{ $pengajuan['id_kategori'] == $kat['id_kategori'] ? 'selected' : '' }}>{{ $kat['nama_kategori'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="aktif" {{ $pengajuan['status'] == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="noaktif" {{ $pengajuan['status'] == 'noaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Pengajuan</label>
                    <input type="date" name="tgl_pengajuan" class="form-control" value="{{ $pengajuan['tgl_pengajuan'] }}" required>
                </div>
                <button class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('prodi.pengajuan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
@stop
