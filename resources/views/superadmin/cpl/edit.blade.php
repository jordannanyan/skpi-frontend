@extends('adminlte::page')

@section('title', 'Edit CPL')
@include('superadmin.partials.header')


@section('content_header')
    <h1>Edit CPL</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.cpl.update', $cpl['id_cpl']) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    <label>Nama CPL</label>
                    <input type="text" name="nama_cpl" class="form-control" value="{{ old('nama_cpl', $cpl['nama_cpl']) }}" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="aktif" {{ $cpl['status'] === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="noaktif" {{ $cpl['status'] === 'noaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('superadmin.cpl.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@stop
