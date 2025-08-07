@extends('adminlte::page')
@section('title', 'Tambah Isi Capaian')
@include('prodi.partials.header')

@section('content_header')
    <h1>Tambah Isi Capaian</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('prodi.isi_capaian.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="id_cpl_skor">CPL Skor (Mahasiswa)</label>
                    <select name="id_cpl_skor" class="form-control" required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach ($cplSkorList as $skor)
                            <option value="{{ $skor['id_cpl_skor'] }}">{{ $skor['skor_cpl']. " " . $skor['mahasiswa']['nama_mahasiswa'] ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="deskripsi_indo">Deskripsi (Indonesia)</label>
                    <textarea name="deskripsi_indo" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="deskripsi_inggris">Deskripsi (Inggris)</label>
                    <textarea name="deskripsi_inggris" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('prodi.isi_capaian.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@stop