@extends('adminlte::page')
@section('title', 'Tambah CPL Master')
@include('superadmin.partials.header')

@section('content_header')
  <h1>Tambah CPL Master</h1>
@stop

@section('content')
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card">
  <form method="POST" action="{{ route('superadmin.cpl-master.store') }}">
    @csrf
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Prodi <span class="text-danger">*</span></label>
          <select name="id_prodi" class="form-control" required>
            <option value="">-- Pilih Prodi --</option>
            @foreach($prodiList as $p)
              <option value="{{ $p['id_prodi'] }}"
                {{ old('id_prodi') == ($p['id_prodi'] ?? null) ? 'selected' : '' }}>
                {{ $p['nama_prodi'] ?? ('Prodi #' . ($p['id_prodi'] ?? '?')) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Kategori CPL (opsional)</label>
          <select name="id_cpl" class="form-control">
            <option value="">-- Tanpa Kategori --</option>
            @foreach($kategoriCpl as $k)
              <option value="{{ $k['id_cpl'] }}"
                {{ old('id_cpl') == ($k['id_cpl'] ?? null) ? 'selected' : '' }}>
                {{ $k['nama_cpl'] ?? ('CPL #' . ($k['id_cpl'] ?? '?')) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Kode <span class="text-danger">*</span></label>
          <input type="text" name="kode" class="form-control" placeholder="CPL1"
                 value="{{ old('kode') }}" maxlength="20" required>
        </div>

        <div class="col-md-8">
          <label class="form-label">Judul / Nama CPL (opsional)</label>
          <input type="text" name="nama_cpl" class="form-control"
                 value="{{ old('nama_cpl') }}" maxlength="255">
        </div>

        <div class="col-12">
          <label class="form-label">Deskripsi (opsional)</label>
          <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="col-md-4">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            @php $st = old('status', 'aktif'); @endphp
            <option value="aktif"   {{ $st === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="noaktif" {{ $st === 'noaktif' ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      <a href="{{ route('superadmin.cpl-master.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
@stop
