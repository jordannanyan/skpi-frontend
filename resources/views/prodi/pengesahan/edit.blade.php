@extends('adminlte::page')
@section('title', 'Edit Pengesahan')
@include('prodi.partials.header')

@section('content_header')
    <h1>Edit Pengesahan</h1>
@stop
@section('content')
    <form action="{{ route('prodi.pengesahan.update', $pengesahan['id_pengesahan']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Fakultas</label>
                    <select name="id_fakultas" class="form-control" required>
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($fakultas as $item)
                            <option value="{{ $item['id_fakultas'] }}" {{ $item['id_fakultas'] == $pengesahan['id_fakultas'] ? 'selected' : '' }}>{{ $item['nama_fakultas'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pengajuan</label>
                    <select name="id_pengajuan" class="form-control" required>
                        <option value="">-- Pilih Pengajuan --</option>
                        @foreach($pengajuan as $item)
                            <option value="{{ $item['id_pengajuan'] }}" {{ $item['id_pengajuan'] == $pengesahan['id_pengajuan'] ? 'selected' : '' }}>{{ $item['mahasiswa']['nama_mahasiswa'] ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Pengesahan</label>
                    <input type="text" name="nomor_pengesahan" class="form-control" value="{{ $pengesahan['nomor_pengesahan'] }}" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Pengesahan</label>
                    <input type="date" name="tgl_pengesahan" class="form-control" value="{{ $pengesahan['tgl_pengesahan'] }}" required>
                </div>
                <button class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('prodi.pengesahan.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop