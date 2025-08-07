@extends('adminlte::page')
@section('title', 'Edit CPL Skor')
@include('prodi.partials.header')

@section('content_header')
    <h1>Edit CPL Skor</h1>
@stop
@section('content')
    <form action="{{ route('prodi.cpl_skor.update', $cplSkor['id_cpl_skor']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="id_mahasiswa">Mahasiswa</label>
                    <select name="id_mahasiswa" class="form-control" required>
                        @foreach ($mahasiswa as $mhs)
                            <option value="{{ $mhs['id_mahasiswa'] }}" {{ $cplSkor['id_mahasiswa'] == $mhs['id_mahasiswa'] ? 'selected' : '' }}>
                                {{ $mhs['nama_mahasiswa'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_cpl">CPL</label>
                    <select name="id_cpl" class="form-control" required>
                        @foreach ($cpl as $item)
                            <option value="{{ $item['id_cpl'] }}" {{ $cplSkor['id_cpl'] == $item['id_cpl'] ? 'selected' : '' }}>
                                {{ $item['nama_cpl'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="skor_cpl">Skor</label>
                    <input type="number" name="skor_cpl" class="form-control" value="{{ $cplSkor['skor_cpl'] }}" required>
                </div>
                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('prodi.cpl_skor.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop
