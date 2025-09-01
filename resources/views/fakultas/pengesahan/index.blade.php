@extends('adminlte::page')

@section('title', 'Data Pengesahan')

@include('fakultas.partials.header')

@section('content_header')
  <h1>Data Pengesahan per Prodi</h1>
@stop

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="mb-3">
  <a href="{{ route('fakultas.pengesahan.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Tambah Pengesahan SKPI
  </a>
</div>

{{-- Filter / Pencarian --}}
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('fakultas.pengesahan.index') }}" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Cari (Nama/NIM/Username/Nomor Pengesahan)</label>
        <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Ketik kata kunci lalu Enter">
      </div>

      <div class="col-md-3">
        <label class="form-label">Tanggal dari</label>
        <input type="date" name="date_from" class="form-control" value="{{ $date_from ?? '' }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">Tanggal sampai</label>
        <input type="date" name="date_to" class="form-control" value="{{ $date_to ?? '' }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Filter Prodi (opsional)</label>
        <select name="prodi_id" class="form-control">
          <option value="">Semua Prodi</option>
          @foreach($prodiList as $p)
            @php $pid = $p['id_prodi'] ?? null; @endphp
            <option value="{{ $pid }}" {{ (string)($prodi_id ?? '') === (string)$pid ? 'selected' : '' }}>
              {{ $p['nama_prodi'] ?? ('Prodi #'.$pid) }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-12 d-flex gap-2 mt-2">
        <button class="btn btn-primary mr-1"><i class="fas fa-search"></i> Terapkan</button>
        <a href="{{ route('fakultas.pengesahan.index') }}" class="btn btn-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

@isset($total_source)
  <div class="mb-2 text-muted small">
    Menampilkan <strong>{{ $total_filtered ?? 0 }}</strong> dari <strong>{{ $total_source }}</strong> data (setelah filter).
  </div>
@endisset

{{-- Grup per Prodi --}}
@forelse($grouped as $pid => $group)
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <strong>{{ data_get($group, 'prodi.nama_prodi', 'Prodi #'.$pid) }}</strong>
        <span class="badge badge-info ml-2">Total: {{ data_get($group, 'count', 0) }}</span>
      </div>
      <button class="btn btn-sm btn-outline-secondary" type="button"
              data-toggle="collapse" data-target="#prodi-{{ $pid }}" aria-expanded="true">
        Tampilkan/Sembunyikan
      </button>
    </div>

    <div id="prodi-{{ $pid }}" class="collapse show">
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th style="width:60px">#</th>
              <th>Nama Mahasiswa</th>
              <th style="width:120px">NIM</th>
              <th>Nomor Pengesahan</th>
              <th style="width:140px">Tanggal</th>
              <th style="width:220px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($group['items'] as $i => $item)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ data_get($item, 'pengajuan.mahasiswa.nama_mahasiswa', '-') }}</td>
                <td>{{ data_get($item, 'pengajuan.mahasiswa.nim_mahasiswa', '-') }}</td>
                <td>{{ $item['nomor_pengesahan'] ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Str::of($item['tgl_pengesahan'] ?? '-')->limit(10, '') }}</td>
                <td class="text-nowrap">
                  <a href="{{ route('fakultas.pengesahan.edit', $item['id_pengesahan']) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('fakultas.pengesahan.destroy', $item['id_pengesahan']) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Yakin ingin menghapus?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                  </form>
                  <a href="{{ route('fakultas.pengesahan.print', $item['id_pengesahan']) }}" class="btn btn-info btn-sm" target="_blank">Print</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@empty
  <div class="card">
    <div class="card-body text-center text-muted">Tidak ada data pengesahan</div>
  </div>
@endforelse
@stop
