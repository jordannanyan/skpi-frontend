@extends('adminlte::page')
@section('title', 'Tambah Kategori')
@include('prodi.partials.header')

@section('content_header')
    <h1>Tambah Kategori</h1>
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
            <form action="{{ route('prodi.kategori.store') }}" method="POST">
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
                <a href="{{ route('prodi.kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@stop
