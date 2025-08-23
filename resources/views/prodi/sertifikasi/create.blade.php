@extends('adminlte::page')
@section('title', 'Tambah Sertifikasi')
@include('prodi.partials.header')

@section('content_header')
<h1>Tambah Sertifikasi</h1>
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
<form action="{{ route('prodi.sertifikasi.store') }}" method="POST" enctype="multipart/form-data">
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
                <label>Nama Sertifikasi</label>
                <input type="text" name="nama_sertifikasi" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="kategori_sertifikasi">Kategori Sertifikasi</label>
                <select id="kategori_sertifikasi" name="kategori_sertifikasi" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @php $oldCat = old('kategori_sertifikasi'); @endphp
                    <option value="KEAHLIAN" {{ $oldCat === 'KEAHLIAN' ? 'selected' : '' }}>KEAHLIAN</option>
                    <option value="PELATIHAN/SEMINAR/WORKSHOP" {{ $oldCat === 'PELATIHAN/SEMINAR/WORKSHOP' ? 'selected' : '' }}>
                        PELATIHAN/SEMINAR/WORKSHOP
                    </option>
                    <option value="PRESTASI DAN PENGHARGAAN" {{ $oldCat === 'PRESTASI DAN PENGHARGAAN' ? 'selected' : '' }}>
                        PRESTASI DAN PENGHARGAAN
                    </option>
                    <option value="PENGALAMAN ORGANISASI" {{ $oldCat === 'PENGALAMAN ORGANISASI' ? 'selected' : '' }}>
                        PENGALAMAN ORGANISASI
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="file_sertifikat" id="fileLabel">File Sertifikat</label>
                <input type="file" id="file_sertifikat" name="file_sertifikat" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('prodi.sertifikasi.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    $(function () {
        // Inisialisasi Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: 'resolve',
            allowClear: true,
            placeholder: function(){
                return $(this).data('placeholder') || 'Cari...';
            },
            matcher: function(params, data) {
                if ($.trim(params.term) === '') { return data; }
                if (typeof data.text === 'undefined') { return null; }
                const term = params.term.toLowerCase();
                const text = data.text.toLowerCase();
                return text.indexOf(term) > -1 ? data : null;
            }
        });

        // Ubah label File Sertifikat kalau pilih "PENGALAMAN ORGANISASI"
        function updateFileLabel() {
            if ($('#kategori_sertifikasi').val() === 'PENGALAMAN ORGANISASI') {
                $('#fileLabel').text('File Sertifikat/SK Organisasi');
            } else {
                $('#fileLabel').text('File Sertifikat');
            }
        }

        // jalankan saat halaman load
        updateFileLabel();

        // jalankan saat kategori berubah
        $('#kategori_sertifikasi').on('change', updateFileLabel);
    });
</script>
@endsection
