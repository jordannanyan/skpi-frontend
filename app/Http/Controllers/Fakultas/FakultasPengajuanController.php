<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasPengajuanController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
    }

    /** Ambil id_fakultas dari session->id saat role=fakultas */
    private function getFakultasId(): ?int
    {
        if (Session::get('role') !== 'fakultas') return null;
        $id = Session::get('id'); // diset saat login sebagai id_fakultas
        return is_numeric($id) ? (int) $id : null;
    }

    public function index(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) {
            return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);
        }

        try {
            $apiBase = env('API_BASE_URL', 'http://127.0.0.1:8000/api');

            // 1) Ambil daftar prodi milik fakultas ini (untuk label & filter prodi)
            $prodiResp = Http::withToken($token)->get("{$apiBase}/prodi", [
                'id_fakultas' => $fakultasId
            ]);
            if ($prodiResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            $prodiList = $prodiResp->successful() ? ($prodiResp->json('data') ?? []) : [];

            // Map prodi by id for quick lookup
            $prodiMap = [];
            foreach ($prodiList as $p) {
                if (isset($p['id_prodi'])) {
                    $prodiMap[(int)$p['id_prodi']] = $p;
                }
            }

            // 2) Ambil pengajuan milik fakultas ini
            $response = Http::withToken($token)->get("{$apiBase}/pengajuan", [
                'id_fakultas' => $fakultasId,
            ]);
            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $source       = $response->successful() ? ($response->json('data') ?? []) : [];
            $totalSource  = count($source);

            // 3) Ambil filter dari query string
            $q         = trim((string) $request->get('q', ''));         // nama/nim/username
            $dateFrom  = $request->get('date_from');                    // YYYY-MM-DD
            $dateTo    = $request->get('date_to');                      // YYYY-MM-DD
            $status    = $request->get('status');                       // 'aktif' | 'noaktif' | ''
            $prodiId   = $request->get('prodi_id');                     // optional filter

            // 4) Terapkan filter sisi FE
            $filtered = array_values(array_filter($source, function ($row) use ($q, $dateFrom, $dateTo, $status, $prodiId) {
                $ok = true;

                if ($q !== '') {
                    $hay = mb_strtolower(
                        ($row['mahasiswa']['nama_mahasiswa'] ?? '') . ' ' .
                            ($row['mahasiswa']['nim_mahasiswa'] ?? '') . ' ' .
                            ($row['mahasiswa']['username'] ?? '')
                    );
                    $ok = $ok && str_contains($hay, mb_strtolower($q));
                }

                if ($status !== null && $status !== '') {
                    $ok = $ok && (($row['status'] ?? null) === $status);
                }

                // tanggal pengajuan → yyyy-mm-dd (10 chars)
                $tgl = substr((string)($row['tgl_pengajuan'] ?? ''), 0, 10);
                if ($dateFrom) $ok = $ok && ($tgl >= $dateFrom);
                if ($dateTo)   $ok = $ok && ($tgl <= $dateTo);

                if ($prodiId) {
                    $rid = data_get($row, 'mahasiswa.prodi.id_prodi');
                    $ok = $ok && ((string)$rid === (string)$prodiId);
                }

                return $ok;
            }));

            // (Opsional) Urutkan terbaru dulu
            usort($filtered, function ($a, $b) {
                return strcmp(($b['tgl_pengajuan'] ?? ''), ($a['tgl_pengajuan'] ?? ''));
            });

            // 5) Grouping per prodi
            $grouped = [];
            foreach ($filtered as $row) {
                $pid = (int) data_get($row, 'mahasiswa.prodi.id_prodi', 0);
                if (!isset($grouped[$pid])) {
                    $grouped[$pid] = [
                        'prodi' => $prodiMap[$pid] ?? ['id_prodi' => $pid, 'nama_prodi' => 'Prodi #' . $pid],
                        'items' => [],
                        'count' => 0,
                    ];
                }
                $grouped[$pid]['items'][] = $row;
                $grouped[$pid]['count']++;
            }

            // 6) Kirim ke view
            return view('fakultas.pengajuan.index', [
                'grouped'       => $grouped,
                'prodiList'     => $prodiList,
                'q'             => $q,
                'date_from'     => $dateFrom,
                'date_to'       => $dateTo,
                'status'        => $status,
                'prodi_id'      => $prodiId,
                'total_source'  => $totalSource,
                'total_filtered' => count($filtered),
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        // Ambil mahasiswa di bawah fakultas ini
        $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
            'id_fakultas' => $fakultasId,
        ]);
        // Kategori global
        $katResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');

        if ($mhsResp->status() === 401 || $katResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$mhsResp->successful() || !$katResp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat data mahasiswa atau kategori']);
        }

        $mahasiswa = $mhsResp->json('data') ?? [];
        $kategori  = $katResp->json('data') ?? [];

        return view('fakultas.pengajuan.create', compact('mahasiswa', 'kategori'));
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)->post($this->baseUrl, $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$response->successful()) {
            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data pengajuan']);
        }

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil ditambahkan');
    }

    public function show($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) {
            return redirect()->route('fakultas.pengajuan.index')
                ->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);
        }

        try {
            $apiBase = env('API_BASE_URL', 'http://127.0.0.1:8000/api');

            // 1) Detail pengajuan (pastikan termasuk mahasiswa & kategori)
            $pengResp = Http::withToken($token)->get("{$apiBase}/pengajuan/{$id}", [
                'id_fakultas' => $fakultasId,
            ]);
            if ($pengResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$pengResp->successful()) {
                return redirect()->route('fakultas.pengajuan.index')
                    ->withErrors(['error' => $pengResp->json('message') ?? 'Gagal memuat detail pengajuan']);
            }
            $pengajuan = $pengResp->json('data') ?? [];

            // 2) Ambil data Prodi (kalau belum ter-embed)
            $prodi = null;
            $idProdi = data_get($pengajuan, 'mahasiswa.id_prodi');
            if ($idProdi) {
                $prodiResp = Http::withToken($token)->get("{$apiBase}/prodi/{$idProdi}");
                if ($prodiResp->successful()) {
                    $prodi = $prodiResp->json('data');
                }
            }

            // 3) Ambil data pendukung berdasarkan id_mahasiswa
            $idMhs = data_get($pengajuan, 'mahasiswa.id_mahasiswa');
            $cplSkor = $sertifikat = $kerjaPraktek = $tugasAkhir = [];

            if ($idMhs) {
                // 3a) CPL Skor untuk mahasiswa ini
                $cplResp = Http::withToken($token)->get("{$apiBase}/cpl-skor", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($cplResp->successful()) {
                    // endpoint kita sudah include relasi cplMaster (+ kategori)
                    $cplSkor = $cplResp->json('data') ?? [];
                }

                // 3b) Sertifikat
                $sertResp = Http::withToken($token)->get("{$apiBase}/sertifikasi", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($sertResp->successful()) {
                    $sertifikat = $sertResp->json('data') ?? [];
                }

                // 3c) Kerja Praktek
                $kpResp = Http::withToken($token)->get("{$apiBase}/kerja-praktek", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($kpResp->successful()) {
                    $kerjaPraktek = $kpResp->json('data') ?? [];
                }

                // 3d) Tugas Akhir
                $taResp = Http::withToken($token)->get("{$apiBase}/tugas-akhir", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($taResp->successful()) {
                    $tugasAkhir = $taResp->json('data') ?? [];
                }
            }

            return view('fakultas.pengajuan.show', compact(
                'pengajuan',
                'prodi',
                'cplSkor',
                'sertifikat',
                'kerjaPraktek',
                'tugasAkhir'
            ));
        } catch (\Throwable $e) {
            return redirect()->route('fakultas.pengajuan.index')
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $pengResp = Http::withToken($token)->get("{$this->baseUrl}/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);
        $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
            'id_fakultas' => $fakultasId,
        ]);
        $katResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');

        if ($pengResp->status() === 401 || $mhsResp->status() === 401 || $katResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$pengResp->successful() || !$mhsResp->successful() || !$katResp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat data untuk edit pengajuan']);
        }

        $pengajuan = $pengResp->json('data') ?? [];
        $mahasiswa = $mhsResp->json('data') ?? [];
        $kategori  = $katResp->json('data') ?? [];

        return view('fakultas.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$response->successful()) {
            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data pengajuan']);
        }

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=DELETE", [
                'id_fakultas' => $fakultasId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$response->successful()) {
            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus data pengajuan']);
        }

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil dihapus');
    }
}
