@extends('adminlte::page')
@section('title', 'Edit Kategori')
@include('superadmin.partials.header')

@section('content_header')
    <h1>Edit Kategori</h1>
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
            <form action="{{ route('superadmin.kategori.update', $kategori['id_kategori']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori', $kategori['nama_kategori']) }}" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="selesai" {{ $kategori['status'] === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="proses" {{ $kategori['status'] === 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="batal" {{ $kategori['status'] === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('superadmin.kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@stop