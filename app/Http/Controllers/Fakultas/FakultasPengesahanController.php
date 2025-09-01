<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasPengesahanController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
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
            return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');
        }

        try {
            $apiBase = env('API_BASE_URL', 'http://127.0.0.1:8000/api');

            // 1) Ambil daftar prodi milik fakultas (untuk label & filter)
            $prodiResp = Http::withToken($token)->get("{$apiBase}/prodi", [
                'id_fakultas' => $fakultasId,
            ]);
            if ($prodiResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            $prodiList = $prodiResp->successful() ? ($prodiResp->json('data') ?? []) : [];

            // Map prodi by id
            $prodiMap = [];
            foreach ($prodiList as $p) {
                if (isset($p['id_prodi'])) {
                    $prodiMap[(int)$p['id_prodi']] = $p;
                }
            }

            // 2) Ambil pengesahan milik fakultas ini
            $response = Http::withToken($token)->get("{$apiBase}/pengesahan", [
                'id_fakultas' => $fakultasId,
            ]);
            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $source      = $response->successful() ? ($response->json('data') ?? []) : [];
            $totalSource = count($source);

            // 3) Ambil filter dari query string
            $q        = trim((string)$request->get('q', ''));     // nama/nim/username/nomor_pengesahan
            $dateFrom = $request->get('date_from');               // YYYY-MM-DD
            $dateTo   = $request->get('date_to');                 // YYYY-MM-DD
            $prodiId  = $request->get('prodi_id');                // optional

            // 4) Terapkan filter di sisi FE
            $filtered = array_values(array_filter($source, function ($row) use ($q, $dateFrom, $dateTo, $prodiId) {
                $ok = true;

                if ($q !== '') {
                    $hay = mb_strtolower(
                        ($row['pengajuan']['mahasiswa']['nama_mahasiswa'] ?? '') . ' ' .
                            ($row['pengajuan']['mahasiswa']['nim_mahasiswa'] ?? '') . ' ' .
                            ($row['pengajuan']['mahasiswa']['username'] ?? '') . ' ' .
                            ($row['nomor_pengesahan'] ?? '')
                    );
                    $ok = $ok && str_contains($hay, mb_strtolower($q));
                }

                // tanggal pengesahan → yyyy-mm-dd
                $tgl = substr((string)($row['tgl_pengesahan'] ?? ''), 0, 10);
                if ($dateFrom) $ok = $ok && ($tgl >= $dateFrom);
                if ($dateTo)   $ok = $ok && ($tgl <= $dateTo);

                if ($prodiId) {
                    $rid = data_get($row, 'pengajuan.mahasiswa.prodi.id_prodi');
                    $ok = $ok && ((string)$rid === (string)$prodiId);
                }

                return $ok;
            }));

            // (Opsional) Urutkan terbaru dulu
            usort($filtered, function ($a, $b) {
                return strcmp(($b['tgl_pengesahan'] ?? ''), ($a['tgl_pengesahan'] ?? ''));
            });

            // 5) Grouping per prodi
            $grouped = [];
            foreach ($filtered as $row) {
                $pid = (int) data_get($row, 'pengajuan.mahasiswa.prodi.id_prodi', 0);
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

            // 6) Render
            return view('fakultas.pengesahan.index', [
                'grouped'        => $grouped,
                'prodiList'      => $prodiList,
                'q'              => $q,
                'date_from'      => $dateFrom,
                'date_to'        => $dateTo,
                'prodi_id'       => $prodiId,
                'total_source'   => $totalSource,
                'total_filtered' => count($filtered),
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        try {
            // Ambil data fakultas (dibungkus array agar kompatibel dengan view)
            $fakResp  = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$fakultasId}");
            // Ambil semua pengajuan di bawah fakultas ini
            $pengResp = Http::withToken($token)->get("{$this->baseUrl}/pengajuan", [
                'id_fakultas' => $fakultasId,
            ]);
            // Ambil semua pengesahan di bawah fakultas ini (untuk filter)
            $phResp   = Http::withToken($token)->get("{$this->baseUrl}/pengesahan", [
                'id_fakultas' => $fakultasId,
            ]);

            if (in_array(401, [$fakResp->status(), $pengResp->status(), $phResp->status()], true)) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$fakResp->successful() || !$pengResp->successful() || !$phResp->successful()) {
                return back()->with('error', 'Gagal memuat data fakultas/pengajuan/pengesahan');
            }

            $fakultas     = $fakResp->json('data') ? [$fakResp->json('data')] : [];
            $pengajuanAll = $pengResp->json('data') ?? [];
            $pengesahanAll = $phResp->json('data') ?? [];

            // Kumpulkan id pengajuan & id mahasiswa yang sudah punya pengesahan
            $takenPengajuan = [];
            $takenMahasiswa = [];
            foreach ($pengesahanAll as $ph) {
                $idp = (int)($ph['id_pengajuan'] ?? data_get($ph, 'pengajuan.id_pengajuan', 0));
                if ($idp > 0) $takenPengajuan[$idp] = true;

                $idm = (int)(data_get($ph, 'pengajuan.id_mahasiswa') ?? data_get($ph, 'pengajuan.mahasiswa.id_mahasiswa', 0));
                if ($idm > 0) $takenMahasiswa[$idm] = true;
            }

            // Filter: hanya pengajuan tanpa pengesahan & mahasiswanya belum pernah disahkan
            $pengajuan = array_values(array_filter($pengajuanAll, function ($row) use ($takenPengajuan, $takenMahasiswa) {
                $idp = (int)($row['id_pengajuan'] ?? 0);
                $idm = (int)($row['id_mahasiswa'] ?? data_get($row, 'mahasiswa.id_mahasiswa', 0));

                if ($idp <= 0 || $idm <= 0) return false;
                if (isset($takenPengajuan[$idp])) return false;   // pengajuan sudah disahkan
                if (isset($takenMahasiswa[$idm])) return false;   // mahasiswa sudah pernah disahkan
                return true;
            }));

            // (Opsional) urutkan berdasarkan nama mahasiswa
            usort($pengajuan, function ($a, $b) {
                return strcmp(
                    mb_strtolower($a['mahasiswa']['nama_mahasiswa'] ?? ''),
                    mb_strtolower($b['mahasiswa']['nama_mahasiswa'] ?? '')
                );
            });

            return view('fakultas.pengesahan.create', compact('fakultas', 'pengajuan'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) {
            return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);
        }

        // Validasi form (id_fakultas selalu dari session)
        $validated = $request->validate([
            'id_pengajuan'     => 'required|numeric',
            'tgl_pengesahan'   => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        try {
            // 1) Pre-check: apakah pengajuan ini sudah punya pengesahan?
            //    (Menghindari duplikasi di UI; race condition tetap dijaga oleh API)
            $check = Http::withToken($token)->get("{$this->baseUrl}/pengesahan", [
                'id_pengajuan' => $validated['id_pengajuan'],
            ]);

            if ($check->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($check->successful()) {
                $exists = collect($check->json('data') ?? [])->isNotEmpty();
                if ($exists) {
                    return back()
                        ->withInput()
                        ->withErrors(['error' => 'Pengajuan ini sudah memiliki data pengesahan.']);
                }
            }

            // 2) Lanjut simpan
            $payload  = $validated + ['id_fakultas' => $fakultasId];
            $response = Http::withToken($token)->post("{$this->baseUrl}/pengesahan", $payload);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            // 3) Tangani 409 dari API (jaga-jaga race condition)
            if ($response->status() === 409) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => $response->json('message') ?? 'Pengajuan ini sudah memiliki pengesahan.']);
            }

            if ($response->failed()) {
                // Tangkap pesan validasi 422 bila ada
                if ($response->status() === 422) {
                    $apiErrors = $response->json('errors') ?? [];
                    $first = null;
                    if (is_array($apiErrors) && !empty($apiErrors)) {
                        $firstField = reset($apiErrors);
                        $first = is_array($firstField) ? reset($firstField) : (is_string($firstField) ? $firstField : null);
                    }
                    return back()->withInput()->withErrors(['error' => $first ?: ($response->json('message') ?? 'Validasi gagal')]);
                }

                return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data']);
            }

            return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $pengsResp = Http::withToken($token)->get("{$this->baseUrl}/pengesahan/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);
        $fakResp = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$fakultasId}");
        $pengResp = Http::withToken($token)->get("{$this->baseUrl}/pengajuan", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($pengsResp->status() === 401 || $fakResp->status() === 401 || $pengResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$pengsResp->successful() || !$fakResp->successful() || !$pengResp->successful()) {
            return back()->with('error', 'Gagal memuat data untuk edit');
        }

        $pengesahan = $pengsResp->json('data') ?? null;
        $fakultas   = $fakResp->json('data') ? [$fakResp->json('data')] : [];
        $pengajuan  = $pengResp->json('data') ?? [];

        return view('fakultas.pengesahan.edit', compact('pengesahan', 'fakultas', 'pengajuan'));
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_pengajuan'     => 'required|numeric',
            'tgl_pengesahan'   => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/pengesahan/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if ($response->failed()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Gagal memperbarui data');
        }

        return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/pengesahan/{$id}?_method=DELETE", [
                'id_fakultas' => $fakultasId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal menghapus data');
        }

        return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil dihapus');
    }

    public function print($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        try {
            // Sertakan id_fakultas agar backend bisa otorisasi/filtrasi
            $response = Http::withToken($token)->get("{$this->baseUrl}/pengesahan/print/{$id}", [
                'id_fakultas' => $fakultasId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->failed()) {
                return back()->with('error', $response->json('message') ?? 'Gagal mengambil data print');
            }

            $data     = $response->json('data');
            $cpl_data = $response->json('cpl_data');

            return view('superadmin.pengesahan.print', compact('data', 'cpl_data'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
