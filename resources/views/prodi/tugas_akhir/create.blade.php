@extends('adminlte::page')
@section('title', 'Tambah Tugas Akhir')
@section('content_header')
@include('prodi.partials.header')

<h1>Tambah Tugas Akhir</h1>
@stop

{{-- aktifkan plugin Select2 bawaan adminlte --}}
@section('plugins.Select2', true)

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form action="{{ route('prodi.tugas_akhir.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-body">

            {{-- Mahasiswa (searchable) --}}
            <div class="form-group">
                <label>Mahasiswa</label>
                <select name="id_mahasiswa"
                        class="form-control select2 select2bs4"
                        data-placeholder="Cari mahasiswa (nama / NIM / prodi)"
                        style="width: 100%;"
                        required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    @foreach($mahasiswa as $mhs)
                        @php
                            $nim  = $mhs['nim_mahasiswa'] ?? '-';
                            $prodiName = data_get($mhs, 'prodi.nama_prodi');
                            $label = trim(($mhs['nama_mahasiswa'] ?? '-') . ' — ' . $nim . ($prodiName ? " ({$prodiName})" : ''));
                        @endphp
                        <option value="{{ $mhs['id_mahasiswa'] }}"
                                {{ (string)old('id_mahasiswa') === (string)$mhs['id_mahasiswa'] ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Skripsi"   {{ old('kategori') === 'Skripsi' ? 'selected' : '' }}>Skripsi</option>
                    <option value="Tesis"     {{ old('kategori') === 'Tesis' ? 'selected' : '' }}>Tesis</option>
                    <option value="Disertasi" {{ old('kategori') === 'Disertasi' ? 'selected' : '' }}>Disertasi</option>
                </select>
            </div>

            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Halaman Depan</label>
                <input type="file" name="file_halaman_dpn" class="form-control">
            </div>
            <div class="form-group">
                <label>Lembar Pengesahan</label>
                <input type="file" name="file_lembar_pengesahan" class="form-control">
            </div>
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('prodi.tugas_akhir.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    $(function () {
        // Inisialisasi Select2 dengan tema bootstrap4 (match AdminLTE)
        $('.select2').select2({
            theme: 'bootstrap4',
            width: 'resolve',
            allowClear: true,
            placeholder: function(){
                return $(this).data('placeholder') || 'Cari...';
            },
            // biar pencarian juga match bagian tengah/akhir teks
            matcher: function(params, data) {
                if ($.trim(params.term) === '') { return data; }
                if (typeof data.text === 'undefined') { return null; }
                const term = params.term.toLowerCase();
                const text = data.text.toLowerCase();
                return text.indexOf(term) > -1 ? data : null;
            }
        });
    });
</script>
@endsection
