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

<!-- Notification Cards -->
<div class="row">
    @if(!$pengajuan)
        <!-- No Submission Yet -->
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-info-circle"></i> Belum Ada Pengajuan SKPI</h5>
                <p>Anda belum mengajukan SKPI. Silakan buat pengajuan untuk memulai proses.</p>
                <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Buat Pengajuan SKPI
                </a>
            </div>
        </div>
    @else
        <!-- Submission Status Notifications -->
        <div class="col-md-6">
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check"></i> Pengajuan Berhasil Disubmit</h5>
                <p><strong>Tanggal Pengajuan:</strong> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</p>
                <p><strong>Kategori:</strong> {{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}</p>
                <small class="text-muted">Pengajuan berhasil disubmit. Selanjutnya menunggu pengesahan fakultas.</small>
            </div>
        </div>

        <div class="col-md-6">
            @if(!$pengesahan)
                <!-- Waiting for Faculty Approval -->
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-hourglass-half"></i> Menunggu Pengesahan Fakultas</h5>
                    <p>Pengajuan SKPI Anda sedang menunggu pengesahan dari fakultas.</p>
                    <small class="text-muted">Ini adalah tahap terakhir. Harap tunggu pengesahan dari pihak fakultas.</small>
                </div>
            @else
                <!-- SKPI Process Complete -->
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-trophy"></i> SKPI Selesai</h5>
                    <p><strong>Tanggal Pengesahan:</strong> {{ $pengesahan['tgl_pengesahan'] ?? '-' }}</p>
                    <p><strong>Nomor Pengesahan:</strong> {{ $pengesahan['nomor_pengesahan'] ?? '-' }}</p>
                    <p><strong>Fakultas:</strong> {{ data_get($pengesahan, 'fakultas.nama_fakultas', '-') }}</p>
                    <small class="text-success"><strong>Selamat! Proses SKPI Anda telah selesai dan resmi disahkan.</strong></small>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Additional Status Timeline (Optional) -->
@if($pengajuan)
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline Status SKPI</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="time-label">
                        <span class="bg-primary">Proses SKPI</span>
                    </div>
                    
                    <!-- Step 1: Pengajuan -->
                    <div>
                        <i class="fas fa-file-alt bg-success"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $pengajuan['tgl_pengajuan'] ?? '-' }}</span>
                            <h3 class="timeline-header">Pengajuan SKPI Dibuat</h3>
                            <div class="timeline-body">
                                Pengajuan SKPI untuk kategori "{{ data_get($pengajuan, 'kategori.nama_kategori', '-') }}" telah berhasil disubmit.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Pengesahan -->
                    @if($pengesahan)
                        <div>
                            <i class="fas fa-trophy bg-success"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $pengesahan['tgl_pengesahan'] ?? '-' }}</span>
                                <h3 class="timeline-header">SKPI Proses Selesai</h3>
                                <div class="timeline-body">
                                    <strong>SELAMAT!</strong> SKPI telah resmi selesai dan disahkan oleh {{ data_get($pengesahan, 'fakultas.nama_fakultas', 'Fakultas') }} 
                                    dengan nomor pengesahan: <strong>{{ $pengesahan['nomor_pengesahan'] ?? '-' }}</strong>
                                    <br><span class="badge badge-success">PROSES SELESAI</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div>
                            <i class="fas fa-hourglass-half bg-warning"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> Menunggu</span>
                                <h3 class="timeline-header text-warning">Tahap Akhir: Menunggu Pengesahan</h3>
                                <div class="timeline-body">
                                    <strong>Tahap terakhir!</strong> Pengajuan sedang menunggu pengesahan dari pihak fakultas. 
                                    Setelah disahkan, proses SKPI Anda akan selesai.
                                    <br><span class="badge badge-warning">TAHAP TERAKHIR</span>
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