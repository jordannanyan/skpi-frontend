@extends('adminlte::page')
@section('title', 'Tambah Tugas Akhir')
@include('prodi.partials.header')

@section('content_header')
    <h1>Tambah Tugas Akhir</h1>
@stop
@section('content')
    <form action="{{ route('prodi.tugas_akhir.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label>Kategori</label>
                    <input type="text" name="kategori" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Halaman Depan</label>
                    <input type="file" name="file_halaman_dpn" class="form-control">
                </div>
                <div class="form-group">
                    <label>Lembar Pengesahan</label>
                    <input type="file" name="file_lembar_pengesahan" class="form-control">
                </div>
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('prodi.tugas_akhir.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop
