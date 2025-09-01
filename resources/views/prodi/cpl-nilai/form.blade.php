@extends('adminlte::page')
@section('title', 'Input Skor CPL')
@include('prodi.partials.header')

@section('content_header')
  <h1>Input Skor CPL</h1>
@stop

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="card mb-3">
  <div class="card-body">
    <div><strong>Mahasiswa:</strong> {{ $mhs['nama_mahasiswa'] ?? '-' }} ({{ $mhs['nim_mahasiswa'] ?? '-' }})</div>
    <div><strong>Prodi:</strong> {{ $prodi['nama_prodi'] ?? ($mhs['id_prodi'] ?? '-') }}</div>
  </div>
</div>

<div class="card">
  <form method="POST" action="{{ route('prodi.cpl-nilai.submit', $mhs['id_mahasiswa']) }}">
    @csrf
    <div class="card-body table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th style="width:90px">Kode</th>
            <th>Nama CPL</th>
            <th>Deskripsi</th>
            <th style="width:160px">Skor</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cplMasterList as $cm)
            @php
              $idcm = $cm['id_cpl_master'];
              $pref = $skorByMaster[$idcm] ?? null;
            @endphp
            <tr>
              <td class="text-monospace">{{ $cm['kode'] }}</td>
              <td>{{ $cm['nama_cpl'] ?? '-' }}</td>
              <td class="small">{{ $cm['deskripsi'] ?? '-' }}</td>
              <td>
                <input type="number" step="0.01" class="form-control"
                       name="skor[{{ $idcm }}]" value="{{ old('skor.'.$idcm, $pref) }}"
                       placeholder="cth 85.5">
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Belum ada CPL Master untuk prodi ini</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan Semua</button>
      <a href="{{ route('prodi.cpl-nilai.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
</div>
@stop
