<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiCplNilaiController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * Halaman daftar mahasiswa (TERFILTER id_prodi dari session Prodi).
     */
    public function index(Request $request)
    {
        $token   = Session::get('token');
        // Ambil id_prodi dari session (fallback ke 'id' kalau login prodi menyimpan di situ)
        $idProdi = Session::get('id_prodi') ?? Session::get('id');

        if (!$idProdi) {
            return redirect()->route('login')->withErrors(['login' => 'Tidak ditemukan id_prodi pada sesi. Silakan login ulang.']);
        }

        try {
            // Ambil mahasiswa milik prodi ini
            $params = ['id_prodi' => $idProdi];
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", $params);

            if ($mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful()) {
                return back()->withErrors(['error' => $mhsResp->json('message') ?? 'Gagal mengambil data mahasiswa']);
            }

            $mahasiswaList = $mhsResp->json('data') ?? [];

            // Pencarian sederhana (client-side)
            if ($request->filled('q')) {
                $q = mb_strtolower(trim($request->q));
                $mahasiswaList = array_values(array_filter($mahasiswaList, function ($m) use ($q) {
                    return str_contains(mb_strtolower($m['nama_mahasiswa'] ?? ''), $q)
                        || str_contains(mb_strtolower($m['nim_mahasiswa'] ?? ''), $q)
                        || str_contains(mb_strtolower($m['username'] ?? ''), $q);
                }));
            }

            // NOTE: View untuk Prodi
            return view('prodi.cpl-nilai.index', [
                'mahasiswaList' => $mahasiswaList,
                'q'             => $request->q,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Form input skor untuk 1 mahasiswa (HARUS milik prodi pada session).
     */
    public function form($id_mahasiswa)
    {
        $token   = Session::get('token');
        $idProdi = Session::get('id_prodi') ?? Session::get('id');

        if (!$idProdi) {
            return redirect()->route('login')->withErrors(['login' => 'Tidak ditemukan id_prodi pada sesi. Silakan login ulang.']);
        }

        try {
            // Detail mahasiswa
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id_mahasiswa}");
            if ($mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful()) {
                return back()->withErrors(['error' => $mhsResp->json('message') ?? 'Gagal mengambil data mahasiswa']);
            }
            $mhs = $mhsResp->json('data');

            // Guard: mahasiswa harus milik prodi ini
            if (($mhs['id_prodi'] ?? null) != $idProdi) {
                return back()->withErrors(['error' => 'Anda tidak berhak mengakses mahasiswa dari prodi lain.']);
            }

            // Ambil data prodi (opsional, untuk header)
            $prodi = null;
            $prodiResp = Http::withToken($token)->get("{$this->baseUrl}/prodi/{$idProdi}");
            if ($prodiResp->successful()) {
                $prodi = $prodiResp->json('data') ?? null;
            }

            // Daftar CPL master untuk prodi ini
            $cmResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-master", [
                'id_prodi' => $idProdi,
                'status'   => 'aktif',
            ]);
            if (!$cmResp->successful()) {
                return back()->withErrors(['error' => $cmResp->json('message') ?? 'Gagal mengambil CPL Master']);
            }
            $cplMasterList = $cmResp->json('data') ?? [];

            // Skor existing mahasiswa
            $skorResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
                'id_mahasiswa' => $id_mahasiswa,
            ]);
            $existing = $skorResp->successful() ? ($skorResp->json('data') ?? []) : [];

            // Map skor by id_cpl_master
            $skorByMaster = [];
            foreach ($existing as $row) {
                $idcm = $row['id_cpl_master'] ?? null;
                if ($idcm !== null) $skorByMaster[$idcm] = $row['skor_cpl'];
            }

            // NOTE: View untuk Prodi
            return view('prodi.cpl-nilai.form', [
                'mhs'            => $mhs,
                'prodi'          => $prodi,
                'cplMasterList'  => $cplMasterList,
                'skorByMaster'   => $skorByMaster,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Submit skor (bulk) — tetap cek mahasiswa milik prodi ini.
     */
    public function submit(Request $request, $id_mahasiswa)
    {
        $token   = Session::get('token');
        $idProdi = Session::get('id_prodi') ?? Session::get('id');

        if (!$idProdi) {
            return redirect()->route('login')->withErrors(['login' => 'Tidak ditemukan id_prodi pada sesi. Silakan login ulang.']);
        }

        // Validasi input skor
        $request->validate([
            'skor'   => 'required|array',
            'skor.*' => 'nullable|numeric',
        ]);

        // Susun item bulk (skip kosong)
        $items = [];
        foreach ($request->input('skor') as $id_cpl_master => $nilai) {
            if ($nilai === null || $nilai === '') continue;
            $items[] = [
                'id_cpl_master' => (int)$id_cpl_master,
                'skor_cpl'      => (float)$nilai,
            ];
        }
        if (empty($items)) {
            return back()->withErrors(['error' => 'Tidak ada skor yang diisi.']);
        }

        try {
            // Pastikan mahasiswa milik prodi ini
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id_mahasiswa}");
            if ($mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful()) {
                return back()->withErrors(['error' => $mhsResp->json('message') ?? 'Gagal memverifikasi mahasiswa']);
            }
            $mhs = $mhsResp->json('data');
            if (($mhs['id_prodi'] ?? null) != $idProdi) {
                return back()->withErrors(['error' => 'Anda tidak berhak mengubah skor mahasiswa dari prodi lain.']);
            }

            // Kirim bulk ke API
            $resp = Http::withToken($token)->post("{$this->baseUrl}/cpl-skor-bulk", [
                'id_mahasiswa' => (int)$id_mahasiswa,
                'items'        => $items,
            ]);

            if ($resp->successful()) {
                $msg = $resp->json('message') ?? 'Skor CPL berhasil disimpan';
                // NOTE: Route untuk Prodi
                return redirect()->route('prodi.cpl-nilai.form', $id_mahasiswa)->with('success', $msg);
            }

            if ($resp->status() === 422) {
                $apiErrors = $resp->json('errors') ?? [];
                $first = null;
                if (is_array($apiErrors)) {
                    $firstFieldErrors = reset($apiErrors);
                    $first = is_array($firstFieldErrors) ? reset($firstFieldErrors) : (is_string($firstFieldErrors) ? $firstFieldErrors : null);
                }
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($resp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($resp->status() === 409) {
                return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Terjadi konflik data.']);
            }

            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal menyimpan skor CPL']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function detail($id_mahasiswa)
    {
        $token   = Session::get('token');
        $idProdi = Session::get('id_prodi') ?? Session::get('id');

        if (!$idProdi) {
            return redirect()->route('login')->withErrors(['login' => 'Tidak ditemukan id_prodi pada sesi. Silakan login ulang.']);
        }

        try {
            // Ambil mahasiswa
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id_mahasiswa}");
            if ($mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful()) {
                return back()->withErrors(['error' => $mhsResp->json('message') ?? 'Gagal mengambil data mahasiswa']);
            }
            $mhs = $mhsResp->json('data');

            // Guard: mahasiswa harus milik prodi ini
            if (($mhs['id_prodi'] ?? null) != $idProdi) {
                return back()->withErrors(['error' => 'Anda tidak berhak melihat mahasiswa dari prodi lain.']);
            }

            // Info prodi (opsional)
            $prodi = null;
            $prodiResp = Http::withToken($token)->get("{$this->baseUrl}/prodi/{$idProdi}");
            if ($prodiResp->successful()) $prodi = $prodiResp->json('data') ?? null;

            // CPL Master prodi
            $cmResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-master", [
                'id_prodi' => $idProdi,
                'status'   => 'aktif',
            ]);
            if (!$cmResp->successful()) {
                return back()->withErrors(['error' => $cmResp->json('message') ?? 'Gagal mengambil CPL Master']);
            }
            $cplMasterList = $cmResp->json('data') ?? [];

            // Skor existing mahasiswa
            $skorResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
                'id_mahasiswa' => $id_mahasiswa,
            ]);
            $existing = $skorResp->successful() ? ($skorResp->json('data') ?? []) : [];

            // Map skor by id_cpl_master
            $skorByMaster = [];
            foreach ($existing as $row) {
                $idcm = $row['id_cpl_master'] ?? null;
                if ($idcm !== null) $skorByMaster[$idcm] = $row['skor_cpl'];
            }

            // Gabung ke rows detail + hitung statistik sederhana
            $rows = [];
            $sum = 0.0;
            $filled = 0;
            foreach ($cplMasterList as $cm) {
                $nilai = $skorByMaster[$cm['id_cpl_master']] ?? null;
                if ($nilai !== null && $nilai !== '') {
                    $sum += (float)$nilai;
                    $filled++;
                }
                $rows[] = [
                    'id_cpl_master' => $cm['id_cpl_master'],
                    'kode'          => $cm['kode'] ?? '',
                    'nama_cpl'      => $cm['nama_cpl'] ?? '',
                    'deskripsi'     => $cm['deskripsi'] ?? '',
                    'skor_cpl'      => $nilai,
                ];
            }
            $totalCpl = count($cplMasterList);
            $avg = $filled > 0 ? $sum / $filled : null;

            return view('prodi.cpl-nilai.detail', [
                'mhs'           => $mhs,
                'prodi'         => $prodi,
                'rows'          => $rows,
                'totalCpl'      => $totalCpl,
                'filled'        => $filled,
                'avg'           => $avg,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
