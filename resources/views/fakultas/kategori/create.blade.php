@extends('adminlte::page')
@section('title', 'Tambah Kategori')

@include('fakultas.partials.header')

@section('content_header')
    <h1>Tambah Kategori</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('mahasiswa.kategori.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="selesai">Selesai</option>
                        <option value="proses">Proses</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('mahasiswa.kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@stop
