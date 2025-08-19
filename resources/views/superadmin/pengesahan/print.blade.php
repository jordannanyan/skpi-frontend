<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Pendamping Ijazah (SKPI)</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: transparent;
            color: black;
        }

        .page {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto 20px auto;
            padding: 2cm 2cm 2cm 2cm;
            box-sizing: border-box;
            position: relative;
            background: transparent;
        }

        .page::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/background_print.jpg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            z-index: -1;
            pointer-events: none;
        }

        .content-overlay {
            position: relative;
            z-index: 1;
            background: transparent;
            padding: 15px;
            min-height: calc(100% - 30px);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header .ministry {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .university {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }

        .header .address {
            font-size: 9px;
            margin: 8px 0;
            line-height: 1.2;
        }

        .header .faculty {
            font-size: 13px;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }

        .header .faculty-en {
            font-size: 11px;
            font-style: italic;
            margin-bottom: 15px;
        }

        .header .document-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 5px 0;
        }

        .header .document-title-en {
            font-size: 12px;
            font-style: italic;
            margin-bottom: 10px;
        }

        .document-number {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .description {
            text-align: justify;
            margin-bottom: 15px;
            font-size: 11px;
            line-height: 1.4;
        }

        .description-en {
            font-style: italic;
            color: #444;
            margin-top: 8px;
        }

        .section {
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .section.allow-break {
            page-break-inside: auto;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
            page-break-after: avoid;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 11px;
        }

        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
            border: none;
        }

        .info-table td:first-child {
            width: 180px;
            font-weight: bold;
        }

        .info-table .label-en {
            font-style: italic;
            color: #666;
            font-weight: normal;
        }

        .cpl-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }

        .cpl-table th,
        .cpl-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: left;
            vertical-align: top;
        }

        .cpl-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            page-break-after: avoid;
        }

        .cpl-table .center {
            text-align: center;
        }

        .activities {
            margin: 10px 0;
        }

        .activity-item {
            margin: 4px 0;
            text-align: justify;
            font-size: 11px;
        }

        .signature-section {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            margin-left: auto;
        }

        .signature-space {
            height: 50px;
            margin: 15px 0;
        }

        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: transparent;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }

        .bilingual-row td {
            padding: 2px 5px;
        }

        .compact-spacing {
            margin: 8px 0;
        }

        .compact-spacing .section-title {
            margin-bottom: 8px;
        }

        /* Ensure content that doesn't fit goes to next page */
        .section3-content {
            page-break-inside: auto;
        }

        .cpl-section {
            page-break-inside: auto;
        }

        .activities-section {
            page-break-inside: avoid;
        }

        /* Screen styles */
        @media screen {
            body {
                background: #f5f5f5;
                padding: 20px 0;
            }
            
            .page {
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                margin-bottom: 20px;
            }
        }

        /* Print styles */
        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            html, body {
                margin: 0;
                padding: 0;
                font-size: 11pt;
                background: transparent;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .page {
                width: 21cm;
                min-height: 29.7cm;
                margin: 0;
                padding: 1.8cm 1.8cm 1.8cm 1.8cm;
                box-shadow: none;
                page-break-after: always;
                position: relative;
            }

            .page:last-child {
                page-break-after: avoid;
            }

            .page::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url("{{ asset('images/background_print.jpg') }}");
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                z-index: -1;
            }

            .no-print {
                display: none !important;
            }

            .content-overlay {
                background: transparent;
                padding: 15px;
                min-height: calc(100% - 30px);
            }

            .section {
                page-break-inside: avoid;
            }

            .section.allow-break {
                page-break-inside: auto;
            }

            .section3-content {
                page-break-inside: auto;
            }

            .cpl-table {
                page-break-inside: auto;
            }

            .cpl-table thead {
                page-break-after: avoid;
            }

            .signature-section {
                page-break-inside: avoid;
                margin-top: 20px;
            }

            /* Force page break before section 3 if needed */
            .section3-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">Print Document</button>

    <div class="page">
        <div class="content-overlay">
            <div class="header">
                <div class="ministry">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
                <div class="university">UNIVERSITAS PALANGKA RAYA</div>
                <div class="address">
                    Kampus UPR Tunjung Nyaho Jalan Yos Sudarso Palangka Raya (73111) Kalimantan Tengah<br>
                    Telepon/Fax: 0536-3221722, 3220445, 3226878, 32222646, 3229091, 3220446, 3220447<br>
                    Laman: http://www.upr.ac.id
                </div>
                <div class="faculty">FAKULTAS {{ strtoupper($data['fakultas']['nama_fakultas']) }}</div>
                <div class="faculty-en">FACULTY OF {{ strtoupper($data['fakultas']['nama_fakultas']) }}</div>
                <div class="document-title">SURAT KETERANGAN PENDAMPING IJAZAH (SKPI)</div>
                <div class="document-title-en">DIPLOMA SUPPLEMENT</div>
            </div>

            <p class="document-number">Nomor: {{ $data['nomor_pengesahan'] }}</p>

            <div class="description">
                <p>Surat Keterangan Pendamping Ijazah (SKPI) ini dikeluarkan oleh Universitas Palangka Raya sebagai pendamping ijazah yang menerangkan prestasi mahasiswa bidang akademik kurikuler dan kokurikuler, dan ekstrakurikuler</p>
                <p class="description-en">This diploma supplement is issued by University of Palangka Raya to elaborate the diploma supplement holder's performance of curricular, cocurricular, and extracurricular areas</p>
            </div>

            <div class="section compact-spacing">
                <div class="section-title">I. INFORMASI TENTANG IDENTITAS DIRI PEMEGANG SKPI / DIPLOMA SUPPLEMENT HOLDER IDENTITY</div>
                <table class="info-table">
                    <tr class="bilingual-row">
                        <td>1.1. Nama Lengkap<br><span class="label-en">Full Name</span></td>
                        <td>: {{ $data['pengajuan']['mahasiswa']['nama_mahasiswa'] }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>1.2. Tempat, Tanggal Lahir<br><span class="label-en">Place, date of Birth</span></td>
                        <td>: {{ $data['pengajuan']['mahasiswa']['tempat_lahir'] }}, {{ \Carbon\Carbon::parse($data['pengajuan']['mahasiswa']['tanggal_lahir'])->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>1.3. Nomor Induk Mahasiswa<br><span class="label-en">Student Identification Number</span></td>
                        <td>: {{ $data['pengajuan']['mahasiswa']['nim_mahasiswa'] }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>1.4. Tahun Masuk<br><span class="label-en">Year of Admission</span></td>
                        <td>: {{ \Carbon\Carbon::parse($data['pengajuan']['mahasiswa']['tgl_masuk'])->format('Y') }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>1.5. Tahun Lulus<br><span class="label-en">Year of Completion</span></td>
                        <td>: {{ \Carbon\Carbon::parse($data['pengajuan']['mahasiswa']['tgl_keluar'])->format('Y') }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>1.6. Nomor Ijazah<br><span class="label-en">Diploma Serial Number</span></td>
                        <td>: -</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>1.7. Gelar dan Singkatan<br><span class="label-en">Degree</span></td>
                        <td>: Sarjana {{ $data['pengajuan']['mahasiswa']['prodi']['nama_prodi'] }} (S.Kom.)<br><span class="label-en">Bachelor of {{ $data['pengajuan']['mahasiswa']['prodi']['nama_prodi'] }}</span></td>
                    </tr>
                </table>
            </div>

            <div class="section compact-spacing">
                <div class="section-title">II. INFORMASI TENTANG IDENTITAS PENYELENGGARA PROGRAM / INSTITUTION IDENTITY</div>
                <table class="info-table">
                    <tr class="bilingual-row">
                        <td>2.1. SK Pendirian Perguruan Tinggi<br><span class="label-en">Decree of University Establishment</span></td>
                        <td>: Keputusan Presiden No. 5 Tahun 1963</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.2. Nama Perguruan Tinggi<br><span class="label-en">Name of University</span></td>
                        <td>: Universitas Palangka Raya / University of Palangka Raya</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.3. Program Studi<br><span class="label-en">Study Program</span></td>
                        <td>: {{ $data['pengajuan']['mahasiswa']['prodi']['nama_prodi'] }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.4. Jenis dan Jenjang Pendidikan<br><span class="label-en">Type and Level of Education</span></td>
                        <td>: Akademik / Sarjana ({{ $data['pengajuan']['mahasiswa']['prodi']['jenis_jenjang'] }})<br><span class="label-en">Academic / Bachelor Degree</span></td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.5. Jenjang Kualifikasi Sesuai KKNI<br><span class="label-en">National Qualification Framework of Indonesia</span></td>
                        <td>: Level 6</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.6. Persayaratan Penerimaan<br><span class="label-en">Admission Requirements</span></td>
                        <td>: Lulus Pendidikan Menengah Atas/Sederajat<br><span class="label-en">Graduated from high school or similar level of education</span></td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.7. Bahasa Pengantar Kuliah<br><span class="label-en">Language of Instruction</span></td>
                        <td>: {{ $data['pengajuan']['mahasiswa']['prodi']['bahasa'] }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.8. Sistem Penilaian<br><span class="label-en">Grading System</span></td>
                        <td>: {{ $data['pengajuan']['mahasiswa']['prodi']['penilaian'] }}</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.9. Lama Studi Reguler<br><span class="label-en">Regular Length of Study</span></td>
                        <td>: 8 Semester / 8 Semesters</td>
                    </tr>
                    <tr class="bilingual-row">
                        <td>2.10. Jenis dan Jenjang Pendidikan Lanjutan<br><span class="label-en">Access to Further Education</span></td>
                        <td>: {{ $data['pengajuan']['mahasiswa']['prodi']['jenis_lanjutan'] }}</td>
                    </tr>
                </table>
            </div>

            <div class="section section3-content allow-break">
                <div class="section-title">III. INFORMASI TENTANG ISI DAN HASIL YANG DICAPAI / CONTENTS AND RESULTS GAINED</div>

                <div class="cpl-section">
                    <div class="section-title" style="font-size: 11px; margin-top: 15px;">3.1 Capaian Pembelajaran Lulusan (CPL) / Learning Outcome</div>
                    <table class="cpl-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Capaian Pembelajaran Lulusan (CPL)<br><span style="font-style: italic;">Learning Outcome</span></th>
                                <th style="width: 35%;">Deskripsi (Indonesia)</th>
                                <th style="width: 35%;">Deskripsi (English)</th>
                                <th style="width: 10%;">Skor<br><span style="font-style: italic;">Score</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cpl_data as $cpl)
                            @php $isiCount = count($cpl['isi_capaian']); @endphp
                            @if($isiCount > 0)
                            @foreach($cpl['isi_capaian'] as $i => $isi)
                            <tr>
                                @if($i == 0)
                                <td rowspan="{{ $isiCount }}"><strong>{{ $cpl['nama_cpl'] }}</strong></td>
                                @endif
                                <td>{{ $isi['deskripsi_indo'] }}</td>
                                <td>{{ $isi['deskripsi_inggris'] }}</td>
                                @if($i == 0)
                                <td rowspan="{{ $isiCount }}" class="center">{{ $cpl['skor_cpl'] }}</td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td><strong>{{ $cpl['nama_cpl'] }}</strong></td>
                                <td colspan="2">Tidak ada isi capaian</td>
                                <td class="center">{{ $cpl['skor_cpl'] }}</td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="4" class="center">Tidak ada data CPL</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activities-section">
                    <div class="section-title" style="font-size: 11px; margin-top: 20px;">3.2 Aktivitas Prestasi dan Penghargaan Lulusan / Activities, Achievements and Graduated Awards</div>

                    <div style="margin: 10px 0;">
                        <strong>a. Judul Tugas Akhir / Title of Final Project</strong>
                        <div class="activities">
                            @forelse($data['pengajuan']['mahasiswa']['tugas_akhir'] as $ta)
                            <div class="activity-item">{{ $ta['kategori'] }}: "{{ $ta['judul'] }}"</div>
                            @empty
                            <div class="activity-item">Tidak ada data</div>
                            @endforelse
                        </div>
                    </div>

                    <div style="margin: 10px 0;">
                        <strong>b. Kerja Praktek/Magang / Internship Experience</strong>
                        <div class="activities">
                            @forelse($data['pengajuan']['mahasiswa']['kerja_praktek'] as $kp)
                            <div class="activity-item">{{ $kp['nama_kegiatan'] }}</div>
                            @empty
                            <div class="activity-item">Tidak ada data</div>
                            @endforelse
                        </div>
                    </div>

                    <div style="margin: 10px 0;">
                        <strong>c. Sertifikat Keahlian / Certificate of Expertise</strong>
                        <div class="activities">
                            @forelse($data['pengajuan']['mahasiswa']['sertifikasi'] as $s)
                            <div class="activity-item">{{ $s['kategori_sertifikasi'] }}: {{ $s['nama_sertifikasi'] }}</div>
                            @empty
                            <div class="activity-item">Tidak ada data</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    Palangka Raya, {{ \Carbon\Carbon::parse($data['tgl_pengesahan'])->translatedFormat('d F Y') }}<br>
                    Dekan / Dean<br>
                    <div class="signature-space"></div>
                    <strong>{{ $data['fakultas']['nama_dekan'] }}</strong><br>
                    NIP. {{ $data['fakultas']['nip'] }}
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    window.print()
</script>

</html>