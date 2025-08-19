@extends('adminlte::page')
@section('title', 'Edit Isi Capaian')
@include('superadmin.partials.header')

@section('content_header')
    <h1>Edit Isi Capaian</h1>
@stop
@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.isi_capaian.update', $isiCapaian['id_capaian']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="id_cpl_skor">CPL Skor (Mahasiswa)</label>
                    <select name="id_cpl_skor" class="form-control" required>
                        @foreach ($cplSkorList as $skor)
                            <option value="{{ $skor['id_cpl_skor'] }}" {{ $isiCapaian['id_cpl_skor'] == $skor['id_cpl_skor'] ? 'selected' : '' }}>
                                {{ $skor['skor_cpl']. " " .$skor["cpl"]["nama_cpl"]." - ". $skor['mahasiswa']['nama_mahasiswa'] ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="deskripsi_indo">Deskripsi (Indonesia)</label>
                    <textarea name="deskripsi_indo" class="form-control" required>{{ $isiCapaian['deskripsi_indo'] }}</textarea>
                </div>
                <div class="form-group">
                    <label for="deskripsi_inggris">Deskripsi (Inggris)</label>
                    <textarea name="deskripsi_inggris" class="form-control" required>{{ $isiCapaian['deskripsi_inggris'] }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('superadmin.isi_capaian.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@stop
