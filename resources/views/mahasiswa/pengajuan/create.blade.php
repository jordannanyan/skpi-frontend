@extends('adminlte::page')
@section('title', 'Tambah Pengajuan')
@include('mahasiswa.partials.header')

@section('content_header')
    <h1>Tambah Pengajuan</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('mahasiswa.pengajuan.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            {{-- Mahasiswa (readonly) --}}
            <div class="form-group">
                <label>Mahasiswa</label>
                @php
                    // ambil dari session mahasiswa yang login
                    $sessionMahasiswaId   = session('id');                // id_mahasiswa
                    $sessionMahasiswaName = session('nama_mahasiswa');    // nama_mahasiswa

                    // fallback: cari nama di koleksi $mahasiswa jika belum ada di session
                    if (!$sessionMahasiswaName && isset($mahasiswa)) {
                        $row = collect($mahasiswa)->firstWhere('id_mahasiswa', $sessionMahasiswaId);
                        $sessionMahasiswaName = $row['nama_mahasiswa'] ?? 'Mahasiswa';
                    }
                @endphp

                {{-- tampilkan dropdown terkunci agar gaya tetap sama --}}
                <select class="form-control" disabled>
                    <option value="">{{ $sessionMahasiswaName ?? 'Mahasiswa' }}</option>
                </select>

                {{-- nilai yang dikirim ke server --}}
                <input type="hidden" name="id_mahasiswa" value="{{ old('id_mahasiswa', $sessionMahasiswaId) }}">
            </div>

            {{-- Kategori, Status, dan Tanggal dihilangkan dari form.
                 Semuanya akan di-set otomatis di controller:
                 - id_kategori : default (dicari 'SKPI' atau item pertama)
                 - status      : 'aktif'
                 - tgl_pengajuan : tanggal hari ini (server) --}}

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Tanggal pengajuan akan diisi otomatis oleh sistem pada saat Anda menyimpan. Status awal diset aktif.
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('mahasiswa.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop
