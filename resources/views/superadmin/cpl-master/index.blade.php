@extends('adminlte::page')
@section('title', 'CPL Master (Per Prodi)')
@include('superadmin.partials.header')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>CPL Master (Per Prodi)</h1>
    <a href="{{ route('superadmin.cpl-master.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah CPL Master
    </a>
</div>
@stop

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th style="width:90px">Kode</th>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Prodi</th>
          <th style="width:110px">Status</th>
          <th style="width:160px">Diubah</th>
          <th style="width:170px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $row)
          <tr>
            <td class="text-monospace">{{ $row['kode'] ?? '-' }}</td>
            <td>{{ $row['nama_cpl'] ?? '-' }}</td>
            <td>{{ data_get($row, 'kategori.nama_cpl', '-') }}</td>
            <td>{{ data_get($row, 'prodi.nama_prodi', $row['id_prodi'] ?? '-') }}</td>
            <td>
              @php $st = $row['status'] ?? 'aktif'; @endphp
              <span class="badge {{ $st === 'aktif' ? 'badge-success' : 'badge-secondary' }}">{{ strtoupper($st) }}</span>
            </td>
            <td>
              @php $upd = $row['updated_at'] ?? null; @endphp
              {{ $upd ? \Carbon\Carbon::parse($upd)->format('d/m/Y H:i') : '-' }}
            </td>
            <td class="text-nowrap">
              <a href="{{ route('superadmin.cpl-master.edit', $row['id_cpl_master']) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Edit
              </a>
              <form action="{{ route('superadmin.cpl-master.destroy', $row['id_cpl_master']) }}"
                    method="POST" class="d-inline"
                    onsubmit="return confirm('Yakin ingin menghapus CPL Master ini?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" type="submit">
                  <i class="fas fa-trash"></i> Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted">Belum ada data CPL Master</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@stop
