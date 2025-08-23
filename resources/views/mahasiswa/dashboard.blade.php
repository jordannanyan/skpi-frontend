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
    $mhsId = session('id'); 

    $pengajuan = null;
    $pengesahan = null;
    $jumlahDokumen = 0;
    $err = null;

    if ($token && $mhsId) {
        try {
            $respDok = Http::withToken($token)->get("$api/sertifikasi");
            if ($respDok->successful()) {
                $listDok = collect($respDok->json('data') ?? []);
                $jumlahDokumen = $listDok->where('id_mahasiswa', $mhsId)->count();
            }

            $respPeng = Http::withToken($token)->get("$api/pengajuan");
            if ($respPeng->successful()) {
                $listPeng = collect($respPeng->json('data') ?? []);
                $pengajuan = $listPeng
                    ->where('id_mahasiswa', $mhsId)
                    ->sortByDesc(fn($x) => $x['tgl_pengajuan'] ?? $x['created_at'] ?? '')
                    ->first();
            }

            if ($pengajuan) {
                $respSah = Http::withToken($token)->get("$api/pengesahan");
                if ($respSah->successful()) {
                    $listSah = collect($respSah->json('data') ?? []);
                    $pengesahan = $listSah->firstWhere('id_pengajuan', $pengajuan['id_pengajuan'] ?? null);
                }
            }
        } catch (\Throwable $e) {
            $err = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
@endphp

@if($err)
    <div class="alert alert-danger">{{ $err }}</div>
@endif

<div class="row">
    @if(!$pengajuan)
        {{-- Belum ada pengajuan --}}
        @if($jumlahDokumen < 1)
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Dokumen Belum Lengkap</h5>
                    <p>Anda belum mengunggah dokumen pendukung SKPI.
                       Harap lengkapi data sertifikasi, prestasi, atau pengalaman organisasi terlebih dahulu.</p>
                    <a href="{{ route('mahasiswa.sertifikasi.index') }}" class="btn btn-info">
                        <i class="fas fa-upload"></i> Upload Dokumen
                    </a>
                </div>
            </div>
        @else
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Belum Ada Pengajuan SKPI</h5>
                    <p>Anda sudah bisa melakukan pengajuan SKPI.</p>
                    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Pengajuan SKPI
                    </a>
                </div>
            </div>
        @endif
    @else
        {{-- Sudah ada pengajuan --}}
        <div class="col-md-12">
            <div class="alert alert-info">
                <h5><i class="icon fas fa-check"></i> Pengajuan Berhasil Disubmit</h5>
                <p><strong>Tanggal Pengajuan:</strong> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</p>
                <p><strong>Kategori:</strong> {{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}</p>
                <small class="text-muted">Selanjutnya menunggu pengesahan fakultas.</small>
            </div>
        </div>
    @endif
</div>

{{-- TIMELINE ala AdminLTE --}}
@if($pengajuan)
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Timeline Proses SKPI</h3>
            </div>
            <div class="card-body">
                <div class="timeline">

                    {{-- Step 1: Pengajuan dibuat --}}
                    <div>
                        <i class="fas fa-file-alt bg-green"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</span>
                            <h3 class="timeline-header">Pengajuan SKPI Dibuat</h3>
                            <div class="timeline-body">
                                Pengajuan SKPI kategori <strong>{{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}</strong> telah disubmit.
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Menunggu Pengesahan Fakultas --}}
                    @if(!$pengesahan)
                    <div>
                        <i class="fas fa-hourglass-half bg-warning"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> Menunggu</span>
                            <h3 class="timeline-header text-warning">Tahap Akhir: Menunggu Pengesahan</h3>
                            <div class="timeline-body">
                                SKPI Anda sedang menunggu pengesahan dari pihak fakultas.
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Step 3: SKPI selesai --}}
                    @if($pengesahan)
                    <div>
                        <i class="fas fa-trophy bg-success"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $pengesahan['tgl_pengesahan'] ?? '-' }}</span>
                            <h3 class="timeline-header">SKPI Selesai</h3>
                            <div class="timeline-body">
                                SKPI Anda telah disahkan oleh <strong>{{ data_get($pengesahan, 'fakultas.nama_fakultas', '-') }}</strong>
                                dengan nomor <strong>{{ $pengesahan['nomor_pengesahan'] ?? '-' }}</strong>.
                                <br><span class="badge badge-success">PROSES SELESAI</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
