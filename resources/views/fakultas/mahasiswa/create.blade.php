@extends('adminlte::page')

@section('title', 'Tambah Mahasiswa')

@include('fakultas.partials.header')


@section('content_header')
    <h1>Tambah Mahasiswa</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Mahasiswa</h3>
        </div>

        <form action="{{ route('mahasiswa.mahasiswa.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>NIM</label>
                    <input type="text" name="nim_mahasiswa" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama_mahasiswa" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Program Studi</label>
                    <select name="id_prodi" class="form-control" required>
                        <option value="">-- Pilih Prodi --</option>
                        @foreach($prodiList as $prodi)
                            <option value="{{ $prodi['id_prodi'] }}">{{ $prodi['nama_prodi'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>No Telp</label>
                    <input type="text" name="no_telp" class="form-control">
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('mahasiswa.mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@stop
