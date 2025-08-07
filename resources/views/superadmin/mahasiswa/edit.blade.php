@extends('adminlte::page')

@section('title', 'Edit Mahasiswa')
@include('superadmin.partials.header')


@section('content_header')
    <h1>Edit Mahasiswa</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.mahasiswa.update', $mahasiswa['id_mahasiswa']) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nim_mahasiswa">NIM</label>
                    <input type="text" name="nim_mahasiswa" class="form-control" value="{{ old('nim_mahasiswa', $mahasiswa['nim_mahasiswa']) }}" required>
                </div>

                <div class="form-group">
                    <label for="nama_mahasiswa">Nama</label>
                    <input type="text" name="nama_mahasiswa" class="form-control" value="{{ old('nama_mahasiswa', $mahasiswa['nama_mahasiswa']) }}" required>
                </div>

                <div class="form-group">
                    <label for="id_prodi">Program Studi</label>
                    <select name="id_prodi" class="form-control" required>
                        <option value="">-- Pilih Prodi --</option>
                        @foreach($prodiList as $prodi)
                            <option value="{{ $prodi['id_prodi'] }}" {{ $mahasiswa['id_prodi'] == $prodi['id_prodi'] ? 'selected' : '' }}>
                                {{ $prodi['nama_prodi'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tempat_lahir">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $mahasiswa['tempat_lahir']) }}" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $mahasiswa['tanggal_lahir']) }}" required>
                </div>

                <div class="form-group">
                    <label for="no_telp">No Telp</label>
                    <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp', $mahasiswa['no_telp']) }}">
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ old('alamat', $mahasiswa['alamat']) }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('superadmin.mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@stop
