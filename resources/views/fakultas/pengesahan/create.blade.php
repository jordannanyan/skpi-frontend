@extends('adminlte::page')
@section('title', 'Tambah Pengesahan')
@include('fakultas.partials.header')

@section('content_header')
    <h1>Tambah Pengesahan</h1>
@stop
@section('content')
    <form action="{{ route('mahasiswa.pengesahan.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Fakultas</label>
                    <select name="id_fakultas" class="form-control" required>
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($fakultas as $item)
                            <option value="{{ $item['id_fakultas'] }}">{{ $item['nama_fakultas'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pengajuan</label>
                    <select name="id_pengajuan" class="form-control" required>
                        <option value="">-- Pilih Pengajuan --</option>
                        @foreach($pengajuan as $item)
                            <option value="{{ $item['id_pengajuan'] }}">{{ $item['mahasiswa']['nama_mahasiswa'] ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Pengesahan</label>
                    <input type="text" name="nomor_pengesahan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Pengesahan</label>
                    <input type="date" name="tgl_pengesahan" class="form-control" required>
                </div>
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('mahasiswa.pengesahan.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop