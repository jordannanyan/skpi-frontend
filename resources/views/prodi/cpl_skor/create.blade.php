@extends('adminlte::page')
@section('title', 'Tambah CPL Skor')
@include('prodi.partials.header')

@section('content_header')
    <h1>Tambah CPL Skor</h1>
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
    <form action="{{ route('prodi.cpl_skor.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                {{-- Mahasiswa (searchable) --}}
                <div class="form-group">
                    <label for="id_mahasiswa">Mahasiswa</label>
                    @php $oldMhs = (string) old('id_mahasiswa'); @endphp
                    <select name="id_mahasiswa"
                            class="form-control select2 select2bs4"
                            data-placeholder="Cari mahasiswa (nama / NIM / prodi)"
                            style="width: 100%;"
                            required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach ($mahasiswa as $mhs)
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

                <div class="form-group">
                    <label for="id_cpl">CPL</label>
                    <select name="id_cpl" class="form-control" required>
                        <option value="">-- Pilih CPL --</option>
                        @foreach ($cpl as $item)
                            <option value="{{ $item['id_cpl'] }}">{{ $item['nama_cpl'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="skor_cpl">Skor</label>
                    <input type="number" name="skor_cpl" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('prodi.cpl_skor.index') }}" class="btn btn-secondary">Kembali</a>
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
            // pencarian berbasis "contains"
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
