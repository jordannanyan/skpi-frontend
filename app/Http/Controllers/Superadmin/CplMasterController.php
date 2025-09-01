<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CplMasterController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    public function index()
    {
        $token = Session::get('token');

        try {
            $resp = Http::withToken($token)->get("{$this->baseUrl}/cpl-master");

            if ($resp->successful()) {
                $data = $resp->json('data') ?? [];
                return view('superadmin.cpl-master.index', compact('data'));
            }

            if ($resp->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $resp->json('message') ?? 'Gagal mengambil data CPL Master']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        $token = Session::get('token');

        try {
            $prodiResp = Http::withToken($token)->get("{$this->baseUrl}/prodi");
            $cplCatResp = Http::withToken($token)->get("{$this->baseUrl}/cpl"); // kategori (opsional)

            if ($prodiResp->successful() && $cplCatResp->successful()) {
                $prodiList   = $prodiResp->json('data') ?? [];
                $kategoriCpl = $cplCatResp->json('data') ?? []; // berisi 4 kategori jika dipakai
                return view('superadmin.cpl-master.create', compact('prodiList', 'kategoriCpl'));
            }

            if ($prodiResp->status() === 401 || $cplCatResp->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => 'Gagal memuat data Prodi/Kategori CPL']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_prodi'  => 'required|integer',
            'id_cpl'    => 'nullable|integer',       // kategori opsional
            'kode'      => 'required|string|max:20', // CPL1, CPL2, ...
            'nama_cpl'  => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'nullable|in:aktif,noaktif',
        ]);

        try {
            $resp = Http::withToken($token)->post("{$this->baseUrl}/cpl-master", $validated);

            if ($resp->successful()) {
                return redirect()->route('superadmin.cpl-master.index')
                    ->with('success', 'CPL Master berhasil ditambahkan');
            }

            if ($resp->status() === 409) {
                $msg = $resp->json('message') ?? 'Kode CPL sudah ada pada prodi tersebut.';
                return back()->withInput()->withErrors(['error' => $msg]);
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
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal menambahkan CPL Master']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');

        try {
            $rowResp    = Http::withToken($token)->get("{$this->baseUrl}/cpl-master/{$id}");
            $prodiResp  = Http::withToken($token)->get("{$this->baseUrl}/prodi");
            $cplCatResp = Http::withToken($token)->get("{$this->baseUrl}/cpl");

            if ($rowResp->successful() && $prodiResp->successful() && $cplCatResp->successful()) {
                $cplMaster   = $rowResp->json('data');
                $prodiList   = $prodiResp->json('data') ?? [];
                $kategoriCpl = $cplCatResp->json('data') ?? [];
                return view('superadmin.cpl-master.edit', compact('cplMaster', 'prodiList', 'kategoriCpl'));
            }

            if (in_array(401, [$rowResp->status(), $prodiResp->status(), $cplCatResp->status()], true)) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $rowResp->json('message') ?? 'Gagal memuat data CPL Master']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_prodi'  => 'required|integer',
            'id_cpl'    => 'nullable|integer',
            'kode'      => 'required|string|max:20',
            'nama_cpl'  => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'nullable|in:aktif,noaktif',
        ]);

        try {
            $resp = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/cpl-master/{$id}?_method=PUT", $validated);

            if ($resp->successful()) {
                return redirect()->route('superadmin.cpl-master.index')
                    ->with('success', 'CPL Master berhasil diperbarui');
            }

            if ($resp->status() === 409) {
                $msg = $resp->json('message') ?? 'Kode CPL sudah ada pada prodi tersebut.';
                return back()->withInput()->withErrors(['error' => $msg]);
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
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal memperbarui CPL Master']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');

        try {
            $resp = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/cpl-master/{$id}?_method=DELETE");

            if ($resp->successful()) {
                return redirect()->route('superadmin.cpl-master.index')
                    ->with('success', 'CPL Master berhasil dihapus');
            }

            if ($resp->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $resp->json('message') ?? 'Gagal menghapus CPL Master']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
