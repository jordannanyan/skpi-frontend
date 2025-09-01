<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CplNilaiController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    // Halaman pilih prodi & mahasiswa
    public function index(Request $request)
    {
        $token = Session::get('token');

        try {
            // prodi list
            $prodiResp = Http::withToken($token)->get("{$this->baseUrl}/prodi");
            $prodiList = $prodiResp->successful() ? ($prodiResp->json('data') ?? []) : [];

            // filter mahasiswa by id_prodi & q (nama/username)
            $params = [];
            if ($request->filled('id_prodi')) $params['id_prodi'] = $request->id_prodi;
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", $params);

            $mahasiswaList = $mhsResp->successful() ? ($mhsResp->json('data') ?? []) : [];
            // simple search client-side (optional)
            if ($request->filled('q')) {
                $q = mb_strtolower(trim($request->q));
                $mahasiswaList = array_values(array_filter($mahasiswaList, function ($m) use ($q) {
                    return str_contains(mb_strtolower($m['nama_mahasiswa'] ?? ''), $q)
                        || str_contains(mb_strtolower($m['nim_mahasiswa'] ?? ''), $q)
                        || str_contains(mb_strtolower($m['username'] ?? ''), $q);
                }));
            }

            if (in_array(401, [$prodiResp->status(), $mhsResp->status()], true)) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return view('superadmin.cpl-nilai.index', [
                'prodiList'     => $prodiList,
                'mahasiswaList' => $mahasiswaList,
                'filterProdi'   => $request->id_prodi,
                'q'             => $request->q,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    // Form input skor untuk 1 mahasiswa
    public function form($id_mahasiswa)
    {
        $token = Session::get('token');

        try {
            // detail mahasiswa
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id_mahasiswa}");
            if (!$mhsResp->successful()) {
                if ($mhsResp->status() === 401) {
                    return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
                }
                return back()->withErrors(['error' => $mhsResp->json('message') ?? 'Gagal mengambil data mahasiswa']);
            }
            $mhs = $mhsResp->json('data');

            // ambil prodi mahasiswa (kalau show() API tidak include prodi, ambil manual)
            $prodi = null;
            if (!empty($mhs['id_prodi'])) {
                $prodiResp = Http::withToken($token)->get("{$this->baseUrl}/prodi/{$mhs['id_prodi']}");
                $prodi = $prodiResp->successful() ? ($prodiResp->json('data') ?? null) : null;
            }

            // daftar CPL master untuk prodi mahasiswa
            $cmResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-master", [
                'id_prodi' => $mhs['id_prodi'] ?? null,
                'status'   => 'aktif',
            ]);
            if (!$cmResp->successful()) {
                return back()->withErrors(['error' => $cmResp->json('message') ?? 'Gagal mengambil CPL Master']);
            }
            $cplMasterList = $cmResp->json('data') ?? [];

            // skor existing mahasiswa
            $skorResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
                'id_mahasiswa' => $id_mahasiswa,
            ]);
            $existing = $skorResp->successful() ? ($skorResp->json('data') ?? []) : [];

            // map skor by id_cpl_master
            $skorByMaster = [];
            foreach ($existing as $row) {
                $idcm = $row['id_cpl_master'] ?? null;
                if ($idcm !== null) $skorByMaster[$idcm] = $row['skor_cpl'];
            }

            return view('superadmin.cpl-nilai.form', [
                'mhs'            => $mhs,
                'prodi'          => $prodi,
                'cplMasterList'  => $cplMasterList,
                'skorByMaster'   => $skorByMaster,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    // Submit skor (bulk)
    public function submit(Request $request, $id_mahasiswa)
    {
        $token = Session::get('token');

        // skor[]: key = id_cpl_master, value = skor
        $request->validate([
            'skor'   => 'required|array',
            'skor.*' => 'nullable|numeric', // biarkan kosong artinya skip
        ]);

        // susun items untuk API bulk
        $items = [];
        foreach ($request->input('skor') as $id_cpl_master => $nilai) {
            if ($nilai === null || $nilai === '' ) continue; // skip kosong
            $items[] = [
                'id_cpl_master' => (int) $id_cpl_master,
                'skor_cpl'      => (float) $nilai,
            ];
        }

        if (empty($items)) {
            return back()->withErrors(['error' => 'Tidak ada skor yang diisi.']);
        }

        try {
            // prefer bulk endpoint
            $resp = Http::withToken($token)->post("{$this->baseUrl}/cpl-skor-bulk", [
                'id_mahasiswa' => (int) $id_mahasiswa,
                'items'        => $items,
            ]);

            if ($resp->successful()) {
                $msg = $resp->json('message') ?? 'Skor CPL berhasil disimpan';
                return redirect()->route('superadmin.cpl-nilai.form', $id_mahasiswa)->with('success', $msg);
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

            // 409 di bulk umumnya tidak muncul karena upsert, tapi disiapkan
            if ($resp->status() === 409) {
                return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Terjadi konflik data.']);
            }

            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal menyimpan skor CPL']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }
}
