@extends('adminlte::page')

@section('title', 'Tambah CPL')
@include('superadmin.partials.header')


@section('content_header')
    <h1>Tambah CPL</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.cpl.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama CPL</label>
                    <input type="text" name="nama_cpl" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif">Aktif</option>
                        <option value="noaktif">Tidak Aktif</option>
                    </select>
                </div>
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('superadmin.cpl.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@stop
