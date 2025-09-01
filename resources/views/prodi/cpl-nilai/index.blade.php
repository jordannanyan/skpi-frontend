@extends('adminlte::page')
@section('title', 'Input Skor CPL - Pilih Mahasiswa')
@include('prodi.partials.header')

@section('content_header')
<h1>Input Skor CPL — Pilih Mahasiswa</h1>
@stop

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('prodi.cpl-nilai.index') }}" class="row g-3">
      <div class="col-md-10">
        <label class="form-label">Cari (Nama/NIM/Username)</label>
        <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Ketik kata kunci lalu Enter">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th style="width:160px">NIM</th>
          <th>Nama</th>
          <th>Username</th>
          <th style="width:140px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($mahasiswaList as $m)
        <tr>
          <td>{{ $m['nim_mahasiswa'] ?? '-' }}</td>
          <td>{{ $m['nama_mahasiswa'] ?? '-' }}</td>
          <td>{{ $m['username'] ?? '-' }}</td>
          <td class="text-nowrap">
            <a class="btn btn-sm btn-primary"
              href="{{ route('prodi.cpl-nilai.form', $m['id_mahasiswa']) }}">
              <i class="fas fa-pen"></i> Isi Nilai
            </a>
            <a class="btn btn-sm btn-info"
              href="{{ route('prodi.cpl-nilai.detail', $m['id_mahasiswa']) }}">
              <i class="fas fa-list"></i> Detail
            </a>
          </td>

        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center text-muted">Tidak ada data mahasiswa</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@stop