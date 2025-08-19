@extends('adminlte::page')
@section('title', 'Sertifikasi')
@include('mahasiswa.partials.header')

@section('content_header')
    <h1>Daftar Sertifikasi</h1>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('mahasiswa.sertifikasi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Sertifikasi
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @php
        // mapping slug -> label kategori (sesuai requirement)
        $categoryMap = [
            'keahlian'                    => 'KEAHLIAN',
            'pelatihan-seminar-workshop'  => 'PELATIHAN/SEMINAR/WORKSHOP',
            'prestasi-dan-penghargaan'    => 'PRESTASI DAN PENGHARGAAN',
            'pengalaman-organisasi'       => 'PENGALAMAN ORGANISASI',
        ];

        $slugSelected   = request()->route('kategori'); // null jika rute /mahasiswa/sertifikasi (tanpa kategori)
        $labelSelected  = $slugSelected ? ($categoryMap[$slugSelected] ?? null) : null;

        // data asli dari controller
        $rows = collect($data ?? []);

        // filter jika ada kategori di URL
        if ($labelSelected) {
            $rows = $rows->filter(function ($row) use ($labelSelected) {
                return strtoupper($row['kategori_sertifikasi'] ?? '') === $labelSelected;
            })->values();
        }
    @endphp

    {{-- Navigasi kategori (opsional, untuk memudahkan user berpindah) --}}
    <div class="mb-3">
        <a href="{{ route('mahasiswa.sertifikasi.index') }}"
           class="btn btn-outline-secondary btn-sm {{ $slugSelected ? '' : 'active' }}">Semua</a>

        <a href="{{ route('mahasiswa.sertifikasi.kategori', 'keahlian') }}"
           class="btn btn-outline-secondary btn-sm {{ $slugSelected === 'keahlian' ? 'active' : '' }}">Keahlian</a>

        <a href="{{ route('mahasiswa.sertifikasi.kategori', 'pelatihan-seminar-workshop') }}"
           class="btn btn-outline-secondary btn-sm {{ $slugSelected === 'pelatihan-seminar-workshop' ? 'active' : '' }}">
           Pelatihan/Seminar/Workshop
        </a>

        <a href="{{ route('mahasiswa.sertifikasi.kategori', 'prestasi-dan-penghargaan') }}"
           class="btn btn-outline-secondary btn-sm {{ $slugSelected === 'prestasi-dan-penghargaan' ? 'active' : '' }}">
           Prestasi dan Penghargaan
        </a>

        <a href="{{ route('mahasiswa.sertifikasi.kategori', 'pengalaman-organisasi') }}"
           class="btn btn-outline-secondary btn-sm {{ $slugSelected === 'pengalaman-organisasi' ? 'active' : '' }}">
           Pengalaman Organisasi
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($labelSelected)
                <h5 class="mb-3">Kategori: <strong>{{ $labelSelected }}</strong></h5>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Sertifikasi</th>
                        <th>Kategori</th>
                        <th>Sertifikat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $index => $sertifikasi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sertifikasi['mahasiswa']['nama_mahasiswa'] ?? '-' }}</td>
                            <td>{{ $sertifikasi['nama_sertifikasi'] ?? '-' }}</td>
                            <td>{{ $sertifikasi['kategori_sertifikasi'] ?? '-' }}</td>
                            <td>
                                @if(!empty($sertifikasi['file_sertifikat']))
                                    <a href="http://127.0.0.1:8000/storage/{{ $sertifikasi['file_sertifikat'] }}" target="_blank">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('mahasiswa.sertifikasi.edit', $sertifikasi['id_sertifikasi']) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('mahasiswa.sertifikasi.destroy', $sertifikasi['id_sertifikasi']) }}"
                                      method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Data sertifikasi tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
