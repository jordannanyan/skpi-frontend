@extends('adminlte::page')
@section('title', 'Tambah Pengajuan')
@section('content_header')
@include('prodi.partials.header')

    <h1>Tambah Pengajuan</h1>
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
    <form action="{{ route('prodi.pengajuan.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">

                {{-- Mahasiswa (searchable) - SATU-SATUNYA input yang terlihat --}}
                <div class="form-group">
                    <label>Mahasiswa</label>
                    <select name="id_mahasiswa"
                            class="form-control select2 select2bs4"
                            data-placeholder="Cari mahasiswa (nama / NIM / prodi)"
                            style="width: 100%;"
                            required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        @php $oldMhs = (string) old('id_mahasiswa'); @endphp
                        @foreach($mahasiswa as $mhs)
                            @php
                                $nim  = $mhs['nim_mahasiswa'] ?? '-';
                                $prodiName = data_get($mhs, 'prodi.nama_prodi');
                                $label = trim(($mhs['nama_mahasiswa'] ?? '-') . ' — ' . $nim . ($prodiName ? " ({$prodiName})" : ''));
                            @endphp
                            <option value="{{ $mhs['id_mahasiswa'] }}"
                                {{ (string)$mhs['id_mahasiswa'] === $oldMhs ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hidden: kategori selalu 1 --}}
                <input type="hidden" name="id_kategori" value="1">

                {{-- Hidden: status selalu aktif --}}
                <input type="hidden" name="status" value="aktif">

                {{-- Hidden: tanggal dari sistem (timezone aplikasi) --}}
                <input type="hidden" name="tgl_pengajuan" value="{{ old('tgl_pengajuan', now()->toDateString()) }}">

                <button class="btn btn-success">Ajukan SKPI</button>
                <a href="{{ route('prodi.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
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
            // pencarian contains (bukan prefix)
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
