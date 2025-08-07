@extends('adminlte::page')
@section('title', 'Tambah CPL Skor')
@include('prodi.partials.header')

@section('content_header')
    <h1>Tambah CPL Skor</h1>
@stop
@section('content')
    <form action="{{ route('prodi.cpl_skor.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="id_mahasiswa">Mahasiswa</label>
                    <select name="id_mahasiswa" class="form-control" required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach ($mahasiswa as $mhs)
                            <option value="{{ $mhs['id_mahasiswa'] }}">{{ $mhs['nama_mahasiswa'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_cpl">CPL</label>
                    <select name="id_cpl" class="form-control" required>
                        <option value="">-- Pilih CPL --</option>
                        @foreach ($cpl as $item)
                            <option value="{{ $item['id_cpl'] }}">{{ $item['nama_cpl'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="skor_cpl">Skor</label>
                    <input type="number" name="skor_cpl" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('prodi.cpl_skor.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop
