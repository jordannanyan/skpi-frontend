<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Pendamping Ijazah (SKPI)</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 40px;
        }

        h2,
        h3,
        h4,
        h5 {
            text-align: center;
            margin: 0;
        }

        .center {
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
        }

        .info-table,
        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }

        .score-table th,
        .score-table td {
            border: 1px solid #000;
            padding: 4px 8px;
            text-align: left;
        }

        .signature {
            margin-top: 60px;
            width: 100%;
        }

        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <h2>UNIVERSITAS PALANGKA RAYA</h2>
    <h3>FAKULTAS {{ strtoupper($data['fakultas']['nama_fakultas']) }}</h3>
    <h4>SURAT KETERANGAN PENDAMPING IJAZAH</h4>
    <p class="center">Nomor: {{ $data['nomor_pengesahan'] }}</p>

    <p>Yang bertanda tangan di bawah ini, Dekan Fakultas:</p>
    <table class="info-table">
        <tr>
            <td>Nama</td>
            <td>: {{ $data['fakultas']['nama_dekan'] }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>: {{ $data['fakultas']['nip'] }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $data['fakultas']['alamat'] }}</td>
        </tr>
    </table>

    <p>Menerangkan bahwa:</p>
    <table class="info-table">
        <tr>
            <td>Nama</td>
            <td>: {{ $data['pengajuan']['mahasiswa']['nama_mahasiswa'] }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>: {{ $data['pengajuan']['mahasiswa']['nim_mahasiswa'] }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{ $data['pengajuan']['mahasiswa']['prodi']['nama_prodi'] }}</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>: {{ $data['pengajuan']['mahasiswa']['prodi']['fakultas']['nama_fakultas'] }}</td>
        </tr>
        <tr>
            <td>Tanggal Masuk</td>
            <td>: {{ $data['pengajuan']['mahasiswa']['tgl_masuk'] }}</td>
        </tr>
        <tr>
            <td>Tanggal Lulus</td>
            <td>: {{ $data['pengajuan']['mahasiswa']['tgl_keluar'] }}</td>
        </tr>
    </table>

    <p>Telah menempuh pendidikan dan lulus dari Program Sarjana (S1) serta memperoleh pengalaman tambahan sebagai berikut:</p>

    <div class="section-title">A. Kegiatan Kerja Praktek</div>
    @forelse($data['pengajuan']['mahasiswa']['kerja_praktek'] as $kp)
    <p>- {{ $kp['nama_kegiatan'] }}</p>
    @empty
    <p>- Tidak ada data</p>
    @endforelse

    <div class="section-title">B. Tugas Akhir</div>
    @forelse($data['pengajuan']['mahasiswa']['tugas_akhir'] as $ta)
    <p>- {{ $ta['kategori'] }}: "{{ $ta['judul'] }}"</p>
    @empty
    <p>- Tidak ada data</p>
    @endforelse

    <div class="section-title">C. Sertifikasi</div>
    @forelse($data['pengajuan']['mahasiswa']['sertifikasi'] as $s)
    <p>- {{ $s['kategori_sertifikasi'] }}: {{ $s['nama_sertifikasi'] }}</p>
    @empty
    <p>- Tidak ada data</p>
    @endforelse

    <div class="section-title">D. Capaian Pembelajaran Lulusan (CPL)</div>
    <table class="score-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kompetensi</th>
                <th>Deskripsi (Indonesia)</th>
                <th>Deskripsi (English)</th>
                                <th>Skor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cpl_data as $index => $cpl)
            @php $isiCount = count($cpl['isi_capaian']); @endphp
            @if($isiCount > 0)
            @foreach($cpl['isi_capaian'] as $i => $isi)
            <tr>
                @if($i == 0)
                <td rowspan="{{ $isiCount }}">{{ $index + 1 }}</td>
                <td rowspan="{{ $isiCount }}">{{ $cpl['nama_cpl'] }}</td>
                @endif
                <td>{{ $isi['deskripsi_indo'] }}</td>
                <td>{{ $isi['deskripsi_inggris'] }}</td>
                @if($i == 0)
                <td rowspan="{{ $isiCount }}">{{ $cpl['skor_cpl'] }}</td>
                @endif
            </tr>
            @endforeach
            @else
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $cpl['nama_cpl'] }}</td>
                <td colspan="2">Tidak ada isi capaian</td>
                <td>{{ $cpl['skor_cpl'] }}</td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="5">Tidak ada data CPL</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature">
        <tr>
            <td></td>
            <td>
                Palangka Raya, {{ \Carbon\Carbon::parse($data['tgl_pengesahan'])->translatedFormat('d F Y') }}<br>
                Dekan,<br><br><br><br>
                <strong>{{ $data['fakultas']['nama_dekan'] }}</strong><br>
                NIP. {{ $data['fakultas']['nip'] }}
            </td>
        </tr>
    </table>
</body>

</html>