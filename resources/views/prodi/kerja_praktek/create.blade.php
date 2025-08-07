@extends('adminlte::page')
@section('title', 'Tambah Kerja Praktek')
@include('prodi.partials.header')

@section('content_header')
    <h1>Tambah Kerja Praktek</h1>
@stop
@section('content')
    <form action="{{ route('prodi.kerja_praktek.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Mahasiswa</label>
                    <select name="id_mahasiswa" class="form-control" required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($mahasiswa as $mhs)
                            <option value="{{ $mhs['id_mahasiswa'] }}">{{ $mhs['nama_mahasiswa'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>File Sertifikat</label>
                    <input type="file" name="file_sertifikat" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('prodi.kerja_praktek.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop
