
@extends('adminlte::page')

@section('title', 'Tambah Fakultas')
@include('superadmin.partials.header')


@section('content_header')
    <h1>Tambah Fakultas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.fakultas.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Fakultas</label>
                    <input type="text" name="nama_fakultas" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nama Dekan</label>
                    <input type="text" name="nama_dekan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" required></textarea>
                </div>
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('superadmin.fakultas.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@stop