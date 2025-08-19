@extends('adminlte::page')
@section('title', 'Edit Kerja Praktek')
@include('prodi.partials.header')

@section('content_header')
    <h1>Edit Kerja Praktek</h1>
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
    <form action="{{ route('prodi.kerja_praktek.update', $kerja_praktek['id_kerja_praktek']) }}?_method=PUT" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                {{-- Mahasiswa (searchable) --}}
                <div class="form-group">
                    <label>Mahasiswa</label>
                    @php $selectedId = (string) old('id_mahasiswa', $kerja_praktek['id_mahasiswa'] ?? ''); @endphp
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
                                {{ (string)$mhs['id_mahasiswa'] === $selectedId ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" class="form-control" value="{{ $kerja_praktek['nama_kegiatan'] }}" required>
                </div>
                <div class="form-group">
                    <label>File Sertifikat</label><br>
                    <small>Biarkan kosong jika tidak ingin mengubah.</small>
                    <input type="file" name="file_sertifikat" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('prodi.kerja_praktek.index') }}" class="btn btn-secondary">Kembali</a>
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
            // pencarian contains
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
