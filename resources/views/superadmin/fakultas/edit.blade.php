
@extends('adminlte::page')

@section('title', 'Edit Fakultas')
@include('superadmin.partials.header')


@section('content_header')
<h1>Edit Fakultas</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('superadmin.fakultas.update', $fakultas['id_fakultas']) }}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PUT">

            <div class="form-group">
                <label>Nama Fakultas</label>
                <input type="text" name="nama_fakultas" class="form-control" value="{{ old('nama_fakultas', $fakultas['nama_fakultas']) }}" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $fakultas['username']) }}" required>
            </div>
            <div class="form-group">
                <label>Password (Biarkan kosong jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
            </div>

            <div class="form-group">
                <label>Nama Dekan</label>
                <input type="text" name="nama_dekan" class="form-control" value="{{ old('nama_dekan', $fakultas['nama_dekan']) }}" required>
            </div>
            <div class="form-group">
                <label>NIP</label>
                <input type="text" name="nip" class="form-control" value="{{ old('nip', $fakultas['nip']) }}" required>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ old('alamat', $fakultas['alamat']) }}</textarea>
            </div>
            <button class="btn btn-success">Simpan Perubahan</button>
            <a href="{{ route('superadmin.fakultas.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@stop