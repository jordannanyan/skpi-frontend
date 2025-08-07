@extends('adminlte::page')
@section('title', 'Edit Kerja Praktek')
@include('prodi.partials.header')

@section('content_header')
    <h1>Edit Kerja Praktek</h1>
@stop
@section('content')
    <form action="{{ route('prodi.kerja_praktek.update', $kerja_praktek['id_kerja_praktek']) }}?_method=PUT" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Mahasiswa</label>
                    <select name="id_mahasiswa" class="form-control" required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($mahasiswa as $mhs)
                            <option value="{{ $mhs['id_mahasiswa'] }}" {{ $mhs['id_mahasiswa'] == $kerja_praktek['id_mahasiswa'] ? 'selected' : '' }}>{{ $mhs['nama_mahasiswa'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" class="form-control" value="{{ $kerja_praktek['nama_kegiatan'] }}" required>
                </div>
                <div class="form-group">
                    <label>File Sertifikat</label><br>
                    <small>Biarkan kosong jika tidak ingin mengubah.</small>
                    <input type="file" name="file_sertifikat" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('prodi.kerja_praktek.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop
