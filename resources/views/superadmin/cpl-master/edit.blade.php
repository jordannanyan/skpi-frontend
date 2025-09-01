@extends('adminlte::page')
@section('title', 'Edit CPL Master')
@include('superadmin.partials.header')

@section('content_header')
  <h1>Edit CPL Master</h1>
@stop

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card">
  <form method="POST" action="{{ route('superadmin.cpl-master.update', $cplMaster['id_cpl_master']) }}">
    @csrf
    @method('PUT')
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Prodi <span class="text-danger">*</span></label>
          <select name="id_prodi" class="form-control" required>
            @foreach($prodiList as $p)
              @php $pid = $p['id_prodi'] ?? null; @endphp
              <option value="{{ $pid }}"
                {{ old('id_prodi', $cplMaster['id_prodi'] ?? null) == $pid ? 'selected' : '' }}>
                {{ $p['nama_prodi'] ?? ('Prodi #' . $pid) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Kategori CPL (opsional)</label>
          <select name="id_cpl" class="form-control">
            <option value="">-- Tanpa Kategori --</option>
            @foreach($kategoriCpl as $k)
              @php $kid = $k['id_cpl'] ?? null; @endphp
              <option value="{{ $kid }}"
                {{ old('id_cpl', $cplMaster['id_cpl'] ?? null) == $kid ? 'selected' : '' }}>
                {{ $k['nama_cpl'] ?? ('CPL #' . $kid) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Kode <span class="text-danger">*</span></label>
          <input type="text" name="kode" class="form-control" maxlength="20"
                 value="{{ old('kode', $cplMaster['kode'] ?? '') }}" required>
        </div>

        <div class="col-md-8">
          <label class="form-label">Judul / Nama CPL (opsional)</label>
          <input type="text" name="nama_cpl" class="form-control" maxlength="255"
                 value="{{ old('nama_cpl', $cplMaster['nama_cpl'] ?? '') }}">
        </div>

        <div class="col-12">
          <label class="form-label">Deskripsi (opsional)</label>
          <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $cplMaster['deskripsi'] ?? '') }}</textarea>
        </div>

        <div class="col-md-4">
          <label class="form-label">Status</label>
          @php $st = old('status', $cplMaster['status'] ?? 'aktif'); @endphp
          <select name="status" class="form-control">
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
