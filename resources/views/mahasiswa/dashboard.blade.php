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

$api = 'http://127.0.0.1:8000/api';
$token = session('token');
$mhsId = session('id');

$pengajuan = null;
$pengesahan = null;
$jumlahSertifikasi = 0;
$jumlahKerjaPraktek = 0;
$jumlahTugasAkhir = 0;
$totalDokumen = 0;
$err = null;


if ($token && $mhsId) {
    try {
        // Check Sertifikasi documents
        $respSertifikasi = Http::withToken($token)->get("$api/sertifikasi");
        if ($respSertifikasi->successful()) {
            $listSertifikasi = collect($respSertifikasi->json('data') ?? []);
            $jumlahSertifikasi = $listSertifikasi->where('id_mahasiswa', $mhsId)->count();
        }

        // Check Kerja Praktek documents
        $respKerjaPraktek = Http::withToken($token)->get("$api/kerja_praktek");
        if ($respKerjaPraktek->successful()) {
            $listKerjaPraktek = collect($respKerjaPraktek->json('data') ?? []);
            $jumlahKerjaPraktek = $listKerjaPraktek->where('id_mahasiswa', $mhsId)->count();
        }

        // Check Tugas Akhir documents
        $respTugasAkhir = Http::withToken($token)->get("$api/tugas_akhir");
        if ($respTugasAkhir->successful()) {
            $listTugasAkhir = collect($respTugasAkhir->json('data') ?? []);
            $jumlahTugasAkhir = $listTugasAkhir->where('id_mahasiswa', $mhsId)->count();
        }

        $totalDokumen = $jumlahSertifikasi + $jumlahKerjaPraktek + $jumlahTugasAkhir;

        // Check pengajuan - only prodi can create pengajuan for mahasiswa
        $respPeng = Http::withToken($token)->get("$api/pengajuan");
        if ($respPeng->successful()) {
            $listPeng = collect($respPeng->json('data') ?? []);
            
            // Get pengajuan for this student (should only be created by prodi)
            $pengajuan = $listPeng
                ->where('id_mahasiswa', $mhsId)
                ->sortByDesc(fn($x) => $x['tgl_pengajuan'] ?? $x['created_at'] ?? '')
                ->first();
        }

        // Get pengesahan if pengajuan exists
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
    {{-- Document Summary Card --}}
    <div class="col-md-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Dokumen</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-certificate"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sertifikasi</span>
                                <span class="info-box-number">{{ $jumlahSertifikasi }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-briefcase"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Kerja Praktek</span>
                                <span class="info-box-number">{{ $jumlahKerjaPraktek }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-graduation-cap"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tugas Akhir</span>
                                <span class="info-box-number">{{ $jumlahTugasAkhir }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <strong>Total Dokumen: {{ $totalDokumen }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Document Requirement Warning --}}
    @if($totalDokumen < 1)
    <div class="col-md-12">
        <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Dokumen Pendukung Belum Lengkap</h5>
            <p>Anda belum mengunggah dokumen pendukung SKPI. Program Studi memerlukan minimal satu dokumen untuk dapat membuat pengajuan SKPI.</p>
            <p><strong>Dokumen yang dapat diunggah:</strong></p>
            <ul>
                <li>Sertifikasi/Prestasi ({{ $jumlahSertifikasi }} dokumen)</li>
                <li>Kerja Praktek ({{ $jumlahKerjaPraktek }} dokumen)</li>
                <li>Tugas Akhir ({{ $jumlahTugasAkhir }} dokumen)</li>
            </ul>
            <p><small class="text-muted">Harap lengkapi dokumen-dokumen tersebut agar Program Studi dapat memproses pengajuan SKPI Anda.</small></p>
            <div class="mt-2">
                <a href="{{ route('mahasiswa.sertifikasi.index') }}" class="btn btn-info mr-2">
                    <i class="fas fa-certificate"></i> Upload Sertifikasi
                </a>
                <a href="{{ route('mahasiswa.kerja_praktek.index') }}" class="btn btn-success mr-2">
                    <i class="fas fa-briefcase"></i> Upload Kerja Praktek
                </a>
                <a href="{{ route('mahasiswa.tugas_akhir.index') }}" class="btn btn-warning">
                    <i class="fas fa-graduation-cap"></i> Upload Tugas Akhir
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    {{-- Status Pengajuan SKPI --}}
    @if(!$pengajuan)
        <div class="col-md-12">
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info-circle"></i> Status Pengajuan SKPI</h5>
                <p>Belum ada pengajuan SKPI yang dibuat oleh Program Studi untuk Anda.</p>
                <p><small class="text-muted">
                    Pengajuan SKPI hanya dapat dilakukan oleh Program Studi. 
                    Silakan hubungi Program Studi Anda jika membutuhkan pengajuan SKPI.
                </small></p>
            </div>
        </div>
    @elseif($pengajuan && !$pengesahan)
        <div class="col-md-12">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-university"></i> Pengajuan SKPI oleh Program Studi</h5>
                <p><strong>Tanggal Pengajuan:</strong> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</p>
                <p><strong>Kategori:</strong> {{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}</p>
                <p>Program Studi telah membuat pengajuan SKPI untuk Anda.</p>
                <small class="text-muted">Status: Menunggu pengesahan fakultas.</small>
            </div>
        </div>
    @elseif($pengajuan && $pengesahan)
        <div class="col-md-12">
            <div class="alert alert-success">
                <h5><i class="icon fas fa-check-circle"></i> SKPI Telah Selesai</h5>
                <p><strong>Tanggal Pengajuan:</strong> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</p>
                <p><strong>Tanggal Pengesahan:</strong> {{ $pengesahan['tgl_pengesahan'] ?? '-' }}</p>
                <p><strong>Kategori:</strong> {{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}</p>
                <p><strong>Nomor Pengesahan:</strong> {{ $pengesahan['nomor_pengesahan'] ?? '-' }}</p>
                <p>SKPI Anda telah selesai diproses dan disahkan oleh {{ data_get($pengesahan, 'fakultas.nama_fakultas', 'Fakultas') }}.</p>
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
                <div class="card-tools">
                    <span class="badge badge-info">Pengajuan oleh Program Studi</span>
                </div>
            </div>
            <div class="card-body">
                <div class="timeline">

                    {{-- Step 1: Pengajuan dibuat --}}
                    <div>
                        <i class="fas fa-file-alt bg-green"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</span>
                            <h3 class="timeline-header">
                                Pengajuan SKPI Dibuat oleh Program Studi
                            </h3>
                            <div class="timeline-body">
                                Pengajuan SKPI kategori <strong>{{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}</strong> 
                                telah dibuat oleh Program Studi.
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

                    @if(!$pengesahan)
                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection