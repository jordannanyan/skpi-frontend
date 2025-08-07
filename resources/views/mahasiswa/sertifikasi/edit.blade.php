@extends('adminlte::page')
@section('title', 'Edit Sertifikasi')
@include('mahasiswa.partials.header')

@section('content_header')
    <h1>Edit Sertifikasi</h1>
@stop
@section('content')
    <form action="{{ route('mahasiswa.sertifikasi.update', $sertifikasi['id_sertifikasi']) }}?_method=PUT" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Mahasiswa</label>
                    <select name="id_mahasiswa" class="form-control" required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($mahasiswa as $mhs)
                            <option value="{{ $mhs['id_mahasiswa'] }}" {{ $mhs['id_mahasiswa'] == $sertifikasi['id_mahasiswa'] ? 'selected' : '' }}>{{ $mhs['nama_mahasiswa'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Sertifikasi</label>
                    <input type="text" name="nama_sertifikasi" class="form-control" value="{{ $sertifikasi['nama_sertifikasi'] }}" required>
                </div>
                <div class="form-group">
                    <label>Kategori Sertifikasi</label>
                    <input type="text" name="kategori_sertifikasi" class="form-control" value="{{ $sertifikasi['kategori_sertifikasi'] }}" required>
                </div>
                <div class="form-group">
                    <label>File Sertifikat</label><br>
                    <small>Biarkan kosong jika tidak ingin mengubah.</small>
                    <input type="file" name="file_sertifikat" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('mahasiswa.sertifikasi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop
