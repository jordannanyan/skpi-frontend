@extends('adminlte::page')
@section('title', 'Tambah Sertifikasi')
@include('mahasiswa.partials.header')

@section('content_header')
    <h1>Tambah Sertifikasi</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('mahasiswa.sertifikasi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                {{-- Mahasiswa --}}
                <div class="form-group">
                    <label>Mahasiswa</label>
                    @php
                        // ambil dari session mahasiswa yang login
                        $sessionMahasiswaId = session('id'); // id_mahasiswa
                        $sessionMahasiswaName = session('nama_mahasiswa'); // nama_mahasiswa

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

                {{-- Nama Sertifikasi --}}
                <div class="form-group">
                    <label>Nama Sertifikasi</label>
                    <input type="text" name="nama_sertifikasi" class="form-control" required>
                </div>

                {{-- Kategori Sertifikasi --}}
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

                {{-- File Sertifikat --}}
                <div class="form-group">
                    <label for="file_sertifikat" id="fileLabel">File Sertifikat</label>
                    <input type="file" id="file_sertifikat" name="file_sertifikat" class="form-control">
                </div>

                {{-- Tombol --}}
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('mahasiswa.sertifikasi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop

{{-- Script --}}
@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const kategori = document.getElementById("kategori_sertifikasi");
        const fileLabel = document.getElementById("fileLabel");

        function updateLabel() {
            if (kategori.value === "PENGALAMAN ORGANISASI") {
                fileLabel.textContent = "File Sertifikat/SK Organisasi";
            } else {
                fileLabel.textContent = "File Sertifikat";
            }
        }

        // Panggil sekali saat load halaman (biar ikut old value kalau ada error)
        updateLabel();

        // Update setiap kali pilihan berubah
        kategori.addEventListener("change", updateLabel);
    });
</script>
@endpush
