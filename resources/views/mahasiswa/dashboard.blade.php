{{-- resources/views/mahasiswa/dashboard.blade.php --}}
@extends('adminlte::page')

@section('title', 'Dashboard')
@include('mahasiswa.partials.header')

@section('content_header')
    <h1>Dashboard SKPI</h1>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

@php
    use Illuminate\Support\Facades\Http;

    $api   = 'http://127.0.0.1:8000/api';
    $token = session('token');
    $mhsId = session('id'); // id_mahasiswa yang login

    $pengajuan = null;
    $pengesahan = null;
    $err = null;

    if ($token && $mhsId) {
        try {
            // Ambil semua pengajuan, filter milik mahasiswa yg login
            $respPeng = Http::withToken($token)->get("$api/pengajuan");
            if ($respPeng->successful()) {
                $listPeng = collect($respPeng->json('data') ?? []);
                // kalau ada banyak, ambil yang terbaru berdasarkan created_at / tgl_pengajuan
                $pengajuan = $listPeng
                    ->where('id_mahasiswa', $mhsId)
                    ->sortByDesc(fn($x) => $x['tgl_pengajuan'] ?? $x['created_at'] ?? '')
                    ->first();
            } else {
                $err = 'Gagal memuat data pengajuan.';
            }

            // Jika sudah ada pengajuan, cek pengesahannya
            if ($pengajuan) {
                $respSah = Http::withToken($token)->get("$api/pengesahan");
                if ($respSah->successful()) {
                    $listSah = collect($respSah->json('data') ?? []);
                    $pengesahan = $listSah->firstWhere('id_pengajuan', $pengajuan['id_pengajuan'] ?? null);
                } else {
                    $err = 'Gagal memuat data pengesahan.';
                }
            }
        } catch (\Throwable $e) {
            $err = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    } else {
        $err = 'Sesi tidak valid. Silakan login ulang.';
    }
@endphp

@if($err)
    <div class="alert alert-danger">{{ $err }}</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Status Pengajuan</h3>
            </div>
            <div class="card-body">
                @if(!$pengajuan)
                    <p>Belum ada pengajuan SKPI.</p>
                    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary btn-sm">
                        Buat Pengajuan
                    </a>
                @else
                    <ul class="list-unstyled mb-0">
                        <li><strong>Tanggal Pengajuan:</strong> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</li>
                        <li><strong>Status:</strong> <span class="badge badge-info">Diajukan</span></li>
                        <li><strong>Kategori:</strong> {{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}</li>
                    </ul>
                    <a href="{{ route('mahasiswa.pengajuan.index') }}" class="btn btn-outline-secondary btn-sm mt-2">
                        Lihat Pengajuan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Status Pengesahan Fakultas</h3>
            </div>
            <div class="card-body">
                @if(!$pengajuan)
                    <p>Ajukan SKPI terlebih dahulu untuk dapat disahkan.</p>
                @elseif(!$pengesahan)
                    <p>Belum disahkan oleh fakultas.</p>
                @else
                    <ul class="list-unstyled mb-0">
                        <li><strong>Tanggal Pengesahan:</strong> {{ $pengesahan['tgl_pengesahan'] ?? '-' }}</li>
                        <li><strong>Nomor Pengesahan:</strong> {{ $pengesahan['nomor_pengesahan'] ?? '-' }}</li>
                        <li><strong>Fakultas:</strong> {{ data_get($pengesahan, 'fakultas.nama_fakultas', '-') }}</li>
                    </ul>
                    <a href="{{ route('mahasiswa.pengesahan.index') }}" class="btn btn-outline-secondary btn-sm mt-2">
                        Lihat Pengesahan
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
