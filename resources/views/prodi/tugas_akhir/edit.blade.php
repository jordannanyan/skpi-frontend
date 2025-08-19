@extends('adminlte::page')
@section('title', 'Edit Tugas Akhir')
@section('content_header')
@include('prodi.partials.header')

    <h1>Edit Tugas Akhir</h1>
@stop

{{-- aktifkan plugin Select2 bawaan AdminLTE --}}
@section('plugins.Select2', true)

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
    <form action="{{ route('prodi.tugas_akhir.update', $ta['id_tugas_akhir']) }}?_method=PUT" method="POST" enctype="multipart/form-data">
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
                        @php $selectedId = (string) old('id_mahasiswa', $ta['id_mahasiswa'] ?? ''); @endphp
                        @foreach ($mahasiswa as $mhs)
                            @php
                                $nim  = $mhs['nim_mahasiswa'] ?? '-';
                                $prodiName = data_get($mhs, 'prodi.nama_prodi');
                                $label = trim(($mhs['nama_mahasiswa'] ?? '-') . ' — ' . $nim . ($prodiName ? " ({$prodiName})" : ''));
                            @endphp
                            <option value="{{ $mhs['id_mahasiswa'] }}"
                                {{ (string)$mhs['id_mahasiswa'] === $selectedId ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @php $selectedKategori = old('kategori', $ta['kategori'] ?? ''); @endphp
                        <option value="Skripsi"   {{ $selectedKategori === 'Skripsi' ? 'selected' : '' }}>Skripsi</option>
                        <option value="Tesis"     {{ $selectedKategori === 'Tesis' ? 'selected' : '' }}>Tesis</option>
                        <option value="Disertasi" {{ $selectedKategori === 'Disertasi' ? 'selected' : '' }}>Disertasi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control" value="{{ $ta['judul'] }}" required>
                </div>
                <div class="form-group">
                    <label>Halaman Depan</label><br>
                    <small>Biarkan kosong jika tidak ingin mengubah.</small>
                    <input type="file" name="file_halaman_dpn" class="form-control">
                </div>
                <div class="form-group">
                    <label>Lembar Pengesahan</label><br>
                    <small>Biarkan kosong jika tidak ingin mengubah.</small>
                    <input type="file" name="file_lembar_pengesahan" class="form-control">
                </div>
                <button class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('prodi.tugas_akhir.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
    $(function () {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: 'resolve',
            allowClear: true,
            placeholder: function(){
                return $(this).data('placeholder') || 'Cari...';
            },
            // pencarian contains (tidak hanya prefix)
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
