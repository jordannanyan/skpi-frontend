@extends('adminlte::page')

@section('title', 'Detail Pengajuan')
@include('prodi.partials.header')

@section('content_header')
  <h1>Detail Pengajuan</h1>
@stop

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

@php $p = $pengajuan ?? $item ?? []; @endphp

<div class="card">
  <div class="card-body">

    {{-- Ringkasan Pengajuan --}}
    <h5 class="mt-2 mb-3">Ringkasan Pengajuan</h5>
    <table class="table table-bordered">
      <tr>
        <th style="width:220px">ID Pengajuan</th>
        <td>{{ $p['id_pengajuan'] ?? '-' }}</td>
      </tr>
      <tr>
        <th>Kategori</th>
        <td>{{ data_get($p, 'kategori.nama_kategori', '-') }}</td>
      </tr>
      <tr>
        <th>Status</th>
        <td>
          @php $st = data_get($p, 'status', '-'); @endphp
          <span class="badge {{ $st==='aktif' ? 'badge-success' : ($st==='noaktif' ? 'badge-secondary' : 'badge-light') }}">
            {{ strtoupper($st) }}
          </span>
        </td>
      </tr>
      <tr>
        <th>Tanggal Pengajuan</th>
        <td>{{ \Illuminate\Support\Str::of(data_get($p, 'tgl_pengajuan', '-'))->limit(10, '') }}</td>
      </tr>
      <tr>
        <th>Dibuat</th>
        <td>{{ data_get($p, 'created_at', '-') }}</td>
      </tr>
      <tr>
        <th>Diupdate</th>
        <td>{{ data_get($p, 'updated_at', '-') }}</td>
      </tr>
    </table>

    {{-- Data Mahasiswa --}}
    <h5 class="mt-4 mb-3">Data Mahasiswa</h5>
    <table class="table table-bordered">
      <tr>
        <th style="width:220px">NIM</th>
        <td>{{ data_get($p, 'mahasiswa.nim_mahasiswa', '-') }}</td>
      </tr>
      <tr>
        <th>Nama</th>
        <td>{{ data_get($p, 'mahasiswa.nama_mahasiswa', '-') }}</td>
      </tr>
      <tr>
        <th>Tempat / Tanggal Lahir</th>
        <td>
          {{ data_get($p, 'mahasiswa.tempat_lahir', '-') }},
          {{ data_get($p, 'mahasiswa.tanggal_lahir', '-') }}
        </td>
      </tr>
      <tr>
        <th>No. Telp</th>
        <td>{{ data_get($p, 'mahasiswa.no_telp', '-') }}</td>
      </tr>
      <tr>
        <th>Alamat</th>
        <td>{{ data_get($p, 'mahasiswa.alamat', '-') }}</td>
      </tr>
      <tr>
        <th>Tanggal Masuk</th>
        <td>{{ data_get($p, 'mahasiswa.tgl_masuk', '-') }}</td>
      </tr>
      <tr>
        <th>Tanggal Keluar</th>
        <td>{{ data_get($p, 'mahasiswa.tgl_keluar', '-') }}</td>
      </tr>
    </table>

    {{-- Data Prodi --}}
    <h5 class="mt-4 mb-3">Data Prodi</h5>
    <table class="table table-bordered">
      <tr>
        <th style="width:220px">Nama Prodi</th>
        <td>{{ data_get($prodi, 'nama_prodi', '-') }}</td>
      </tr>
      <tr>
        <th>Akreditasi</th>
        <td>{{ data_get($prodi, 'akreditasi', '-') }}</td>
      </tr>
      <tr>
        <th>Jenis/Jenjang</th>
        <td>{{ data_get($prodi, 'jenis_jenjang', '-') }}</td>
      </tr>
      <tr>
        <th>SK Akreditasi</th>
        <td>{{ data_get($prodi, 'sk_akre', '-') }}</td>
      </tr>
      <tr>
        <th>Alamat Prodi</th>
        <td>{{ data_get($prodi, 'alamat', '-') }}</td>
      </tr>
    </table>

    {{-- CPL & Skor --}}
    <h5 class="mt-4 mb-3">CPL & Skor</h5>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="width:90px">Kode</th>
            <th>Nama CPL</th>
            <th style="width:220px">Kategori</th>
            <th style="width:120px">Skor</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cplSkor as $row)
            <tr>
              <td>{{ data_get($row, 'cpl_master.kode', '-') }}</td>
              <td>{{ data_get($row, 'cpl_master.nama', '-') }}</td>
              <td>
                {{ data_get($row, 'cpl_master.kategori.nama_cpl',
                    data_get($row, 'cpl_master.kategori', '-')
                ) }}
              </td>
              <td>{{ data_get($row, 'skor_cpl', '-') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Belum ada skor CPL</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Sertifikat --}}
    <h5 class="mt-4 mb-3">Sertifikat</h5>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Nama Sertifikat</th>
            <th style="width:220px">Penyelenggara</th>
            <th style="width:160px">Nomor / ID</th>
            <th style="width:140px">Tanggal</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sertifikat as $s)
            <tr>
              <td>{{ data_get($s, 'nama_sertifikat', data_get($s, 'nama', '-')) }}</td>
              <td>{{ data_get($s, 'penyelenggara', data_get($s, 'penerbit', '-')) }}</td>
              <td>{{ data_get($s, 'nomor', data_get($s, 'no_sertifikat', '-')) }}</td>
              <td>{{ \Illuminate\Support\Str::of(data_get($s, 'tanggal', data_get($s, 'tgl_sertifikat', '-')))->limit(10, '') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Tidak ada data sertifikat</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Kerja Praktek --}}
    <h5 class="mt-4 mb-3">Kerja Praktek</h5>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Judul</th>
            <th style="width:220px">Tempat</th>
            <th style="width:140px">Mulai</th>
            <th style="width:140px">Selesai</th>
          </tr>
        </thead>
        <tbody>
          @forelse($kerjaPraktek as $kp)
            <tr>
              <td>{{ data_get($kp, 'judul_kp', data_get($kp, 'judul', '-')) }}</td>
              <td>{{ data_get($kp, 'tempat', data_get($kp, 'instansi', '-')) }}</td>
              <td>{{ \Illuminate\Support\Str::of(data_get($kp, 'tanggal_mulai', data_get($kp, 'tgl_mulai', '-')))->limit(10, '') }}</td>
              <td>{{ \Illuminate\Support\Str::of(data_get($kp, 'tanggal_selesai', data_get($kp, 'tgl_selesai', '-')))->limit(10, '') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Tidak ada data kerja praktek</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Tugas Akhir --}}
    <h5 class="mt-4 mb-3">Tugas Akhir</h5>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Judul</th>
            <th style="width:180px">Pembimbing</th>
            <th style="width:120px">Tahun</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tugasAkhir as $ta)
            <tr>
              <td>{{ data_get($ta, 'judul_ta', data_get($ta, 'judul', '-')) }}</td>
              <td>{{ data_get($ta, 'pembimbing', '-') }}</td>
              <td>{{ data_get($ta, 'tahun', '-') }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted">Tidak ada data tugas akhir</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <br>
    <a href="{{ route('prodi.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</div>
@stop
