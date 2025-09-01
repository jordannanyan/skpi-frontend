<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiPengajuanController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        // keep pengajuan base
        $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
    }

    /** Get id_prodi from session->id when role=prodi */
    private function getProdiId(): ?int
    {
        if (Session::get('role') !== 'prodi') return null;
        $id = Session::get('id'); // set at login for role=prodi as id_prodi
        return is_numeric($id) ? (int) $id : null;
    }

    public function index(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) {
            return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);
        }

        try {
            // Ambil pengajuan milik prodi ini dari API
            $resp = Http::withToken($token)->get($this->baseUrl, [
                'id_prodi' => $prodiId,
            ]);

            if ($resp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$resp->successful()) {
                return back()->withErrors(['error' => $resp->json('message') ?? 'Gagal memuat data pengajuan']);
            }

            $source       = $resp->json('data') ?? [];
            $totalSource  = count($source);

            // Ambil filter dari query string
            $q         = trim((string) $request->get('q', ''));          // cari nama/nim/username
            $dateFrom  = $request->get('date_from');                     // YYYY-MM-DD
            $dateTo    = $request->get('date_to');                       // YYYY-MM-DD
            $status    = $request->get('status');                        // 'aktif' | 'noaktif' | ''

            // Terapkan filter di sisi FE
            $data = array_values(array_filter($source, function ($row) use ($q, $dateFrom, $dateTo, $status) {
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

                // Normalisasi tanggal: pakai 10 karakter pertama (yyyy-mm-dd)
                $tgl = substr((string)($row['tgl_pengajuan'] ?? ''), 0, 10);
                if ($dateFrom) $ok = $ok && ($tgl >= $dateFrom);
                if ($dateTo)   $ok = $ok && ($tgl <= $dateTo);

                return $ok;
            }));

            // (Opsional) Urutkan terbaru dulu berdasarkan tgl_pengajuan
            usort($data, function ($a, $b) {
                return strcmp(($b['tgl_pengajuan'] ?? ''), ($a['tgl_pengajuan'] ?? ''));
            });

            return view('prodi.pengajuan.index', [
                'data'         => $data,
                'q'            => $q,
                'date_from'    => $dateFrom,
                'date_to'      => $dateTo,
                'status'       => $status,
                'total_source' => $totalSource,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        try {
            // 1) Ambil semua mahasiswa milik prodi ini
            $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
                'id_prodi' => $prodiId,
            ]);

            // 2) Ambil semua pengajuan milik prodi ini
            $pengResp = Http::withToken($token)->get($this->baseUrl, [
                'id_prodi' => $prodiId,
            ]);

            if (in_array(401, [$mhsResp->status(), $pengResp->status()], true)) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful() || !$pengResp->successful()) {
                return back()->withErrors(['error' => 'Gagal memuat data mahasiswa atau pengajuan']);
            }

            $mahasiswa = $mhsResp->json('data') ?? [];
            $pengajuan = $pengResp->json('data') ?? [];

            // 3) Kumpulkan id_mahasiswa yang sudah punya pengajuan (sekali seumur hidup sesuai revisi 6)
            $takenSet = [];
            foreach ($pengajuan as $p) {
                if (isset($p['id_mahasiswa'])) {
                    $takenSet[(int)$p['id_mahasiswa']] = true;
                }
            }

            // 4) Filter mahasiswa: hanya tampilkan yang BELUM punya pengajuan
            $mahasiswa = array_values(array_filter($mahasiswa, function ($m) use ($takenSet) {
                $idm = (int)($m['id_mahasiswa'] ?? 0);
                return $idm > 0 && !isset($takenSet[$idm]);
            }));

            // 5) Kategori (jika perlu untuk form)
            $katResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');
            if ($katResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$katResp->successful()) {
                return back()->withErrors(['error' => 'Gagal memuat data kategori']);
            }
            $kategori = $katResp->json('data') ?? [];

            return view('prodi.pengajuan.create', compact('mahasiswa', 'kategori'));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function show($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        try {
            $apiBase = env('API_BASE_URL', 'http://127.0.0.1:8000/api');

            // Detail pengajuan (pastikan include mahasiswa & kategori)
            $resp = Http::withToken($token)->get("{$this->baseUrl}/{$id}", [
                'id_prodi' => $prodiId,
            ]);

            if ($resp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$resp->successful()) {
                return redirect()->route('prodi.pengajuan.index')
                    ->withErrors(['error' => $resp->json('message') ?? 'Gagal memuat detail pengajuan']);
            }

            $pengajuan = $resp->json('data') ?? [];

            // Ambil data Prodi (kalau perlu ditampilkan rinci)
            $prodi = null;
            $idProdi = data_get($pengajuan, 'mahasiswa.id_prodi', $prodiId);
            if ($idProdi) {
                $prodiResp = Http::withToken($token)->get("{$apiBase}/prodi/{$idProdi}");
                if ($prodiResp->successful()) {
                    $prodi = $prodiResp->json('data');
                }
            }

            // Ambil data pendukung berdasarkan id_mahasiswa
            $idMhs        = data_get($pengajuan, 'mahasiswa.id_mahasiswa');
            $cplSkor      = [];
            $sertifikat   = [];
            $kerjaPraktek = [];
            $tugasAkhir   = [];

            if ($idMhs) {
                // CPL Skor
                $cplResp = Http::withToken($token)->get("{$apiBase}/cpl-skor", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($cplResp->successful()) {
                    $cplSkor = $cplResp->json('data') ?? [];
                }

                // Sertifikasi
                $sertResp = Http::withToken($token)->get("{$apiBase}/sertifikasi", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($sertResp->successful()) {
                    $sertifikat = $sertResp->json('data') ?? [];
                }

                // Kerja Praktek
                $kpResp = Http::withToken($token)->get("{$apiBase}/kerja-praktek", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($kpResp->successful()) {
                    $kerjaPraktek = $kpResp->json('data') ?? [];
                }

                // Tugas Akhir
                $taResp = Http::withToken($token)->get("{$apiBase}/tugas-akhir", [
                    'id_mahasiswa' => $idMhs,
                ]);
                if ($taResp->successful()) {
                    $tugasAkhir = $taResp->json('data') ?? [];
                }
            }

            return view('prodi.pengajuan.show', compact(
                'pengajuan',
                'prodi',
                'cplSkor',
                'sertifikat',
                'kerjaPraktek',
                'tugasAkhir'
            ));
        } catch (\Throwable $e) {
            return redirect()->route('prodi.pengajuan.index')
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function store(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        // If your API accepts id_prodi in body, include it; if not, it will still be
        // inferred from id_mahasiswa on backend.
        $payload = $validated + ['id_prodi' => $prodiId];

        $resp = Http::withToken($token)->post($this->baseUrl, $payload);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal menambahkan data pengajuan']);
        }

        return redirect()->route('prodi.pengajuan.index')->with('success', 'Data pengajuan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $pengResp = Http::withToken($token)->get("{$this->baseUrl}/{$id}", [
            'id_prodi' => $prodiId,
        ]);
        $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
            'id_prodi' => $prodiId,
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

        return view('prodi.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        $payload = $validated + ['id_prodi' => $prodiId];

        $resp = Http::withToken($token)->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=PUT", $payload);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal memperbarui data pengajuan']);
        }

        return redirect()->route('prodi.pengajuan.index')->with('success', 'Data pengajuan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $resp = Http::withToken($token)->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=DELETE", [
                'id_prodi' => $prodiId, // pass along if your backend authorizes by prodi
            ]);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return back()->withErrors(['error' => $resp->json('message') ?? 'Gagal menghapus data pengajuan']);
        }

        return redirect()->route('prodi.pengajuan.index')->with('success', 'Data pengajuan berhasil dihapus');
    }
}
