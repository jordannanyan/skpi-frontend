@extends('adminlte::page')

@section('title', 'Data Pengajuan')

@include('prodi.partials.header')

@section('content_header')
<h1>Data Pengajuan</h1>
@stop

@section('content')
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('prodi.pengajuan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Pengajuan SKPI
    </a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

{{-- Filter / Pencarian --}}
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('prodi.pengajuan.index') }}" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Cari (Nama/NIM/Username)</label>
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

      <div class="col-md-2">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          @php $st = $status ?? ''; @endphp
          <option value="">Semua</option>
          <option value="aktif"   {{ $st==='aktif' ? 'selected' : '' }}>Aktif</option>
          <option value="noaktif" {{ $st==='noaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

      <div class="col-12 d-flex gap-2 mt-2">
        <button class="btn btn-primary mr-1"><i class="fas fa-search"></i> Terapkan</button>
        <a href="{{ route('prodi.pengajuan.index') }}" class="btn btn-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    @isset($total_source)
      <div class="mb-2 text-muted small">
        Menampilkan <strong>{{ count($data) }}</strong> dari <strong>{{ $total_source }}</strong> data (setelah filter)
      </div>
    @endisset

    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th style="width:60px">#</th>
          <th>Nama Mahasiswa</th>
          <th style="width:140px">Tanggal</th>
          <th style="width:120px">Status</th>
          <th style="width:200px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($data as $index => $item)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ data_get($item, 'mahasiswa.nama_mahasiswa', '-') }}</td>
            <td>{{ \Illuminate\Support\Str::of($item['tgl_pengajuan'] ?? '-')->limit(10, '') }}</td>
            <td>
              @php $st = $item['status'] ?? '-'; @endphp
              <span class="badge {{ $st==='aktif' ? 'badge-success' : ($st==='noaktif' ? 'badge-secondary' : 'badge-light') }}">
                {{ strtoupper($st) }}
              </span>
            </td>
            <td class="text-nowrap">
              <a href="{{ route('prodi.pengajuan.show', $item['id_pengajuan']) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> Detail
              </a>

              <a href="{{ route('prodi.pengajuan.edit', $item['id_pengajuan']) }}" class="btn btn-sm btn-warning">
                Edit
              </a>

              <form action="{{ route('prodi.pengajuan.destroy', $item['id_pengajuan']) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Yakin ingin menghapus?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted">Tidak ada data pengajuan</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@stop
