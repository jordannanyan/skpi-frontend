@extends('adminlte::page')
@section('title', 'Detail Nilai CPL Mahasiswa')
@include('prodi.partials.header')

@section('content_header')
  <h1>Detail Nilai CPL</h1>
@stop

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="card mb-3">
  <div class="card-body">
    <div><strong>Mahasiswa:</strong> {{ $mhs['nama_mahasiswa'] ?? '-' }} ({{ $mhs['nim_mahasiswa'] ?? '-' }})</div>
    <div><strong>Prodi:</strong> {{ $prodi['nama_prodi'] ?? ($mhs['id_prodi'] ?? '-') }}</div>
    <div class="mt-2">
      <span class="badge badge-info">Total CPL: {{ $totalCpl }}</span>
      <span class="badge badge-success">Terisi: {{ $filled }}</span>
      <span class="badge badge-secondary">Rata-rata: {{ $avg !== null ? number_format($avg, 2) : '-' }}</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th style="width:90px">Kode</th>
          <th>Nama CPL</th>
          <th>Deskripsi</th>
          <th style="width:140px">Skor</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td class="text-monospace">{{ $r['kode'] }}</td>
            <td>{{ $r['nama_cpl'] ?: '-' }}</td>
            <td class="small">{{ $r['deskripsi'] ?: '-' }}</td>
            <td>
              @if($r['skor_cpl'] !== null && $r['skor_cpl'] !== '')
                {{ rtrim(rtrim(number_format((float)$r['skor_cpl'], 2, '.', ''), '0'), '.') }}
              @else
                <span class="text-muted">Belum diisi</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted">Belum ada CPL Master untuk prodi ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    <a href="{{ route('prodi.cpl-nilai.index') }}" class="btn btn-secondary">Kembali</a>
    <a href="{{ route('prodi.cpl-nilai.form', $mhs['id_mahasiswa']) }}" class="btn btn-primary">
      <i class="fas fa-pen"></i> Isi / Ubah Nilai
    </a>
  </div>
</div>
@stop
