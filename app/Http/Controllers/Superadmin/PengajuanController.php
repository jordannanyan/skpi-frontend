<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PengajuanController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        // Base endpoint untuk resource pengajuan di API
        $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
    }

    public function index()
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)->get($this->baseUrl);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                return view('superadmin.pengajuan.index', compact('data'));
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data pengajuan']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function create()
    {
        $token = Session::get('token');

        try {
            $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa');
            $katResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');

            if ($mhsResp->successful() && $katResp->successful()) {
                $mahasiswa = $mhsResp->json('data') ?? [];
                $kategori  = $katResp->json('data') ?? [];
                return view('superadmin.pengajuan.create', compact('mahasiswa', 'kategori'));
            }

            if ($mhsResp->status() === 401 || $katResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => 'Gagal memuat data mahasiswa/kategori']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        try {
            $response = Http::withToken($token)->post($this->baseUrl, $validated);

            if ($response->successful()) {
                return redirect()
                    ->route('superadmin.pengajuan.index')
                    ->with('success', 'Data pengajuan berhasil ditambahkan');
            }

            // ⚠️ Tangani 409 conflict agar tidak tampil success
            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Pengajuan sudah ada untuk mahasiswa ini.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            // Tangani validasi API (422) dan auth (401)
            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                $first = is_array($apiErrors) ? head(head($apiErrors)) : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            // Fallback error umum
            $msg = $response->json('message') ?? 'Gagal menambahkan data pengajuan';
            return back()->withInput()->withErrors(['error' => $msg]);

        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');

        try {
            $pengResp = Http::withToken($token)->get("{$this->baseUrl}/{$id}");
            $mhsResp  = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa');
            $katResp  = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');

            if ($pengResp->successful() && $mhsResp->successful() && $katResp->successful()) {
                $pengajuan = $pengResp->json('data');
                $mahasiswa = $mhsResp->json('data') ?? [];
                $kategori  = $katResp->json('data') ?? [];
                return view('superadmin.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
            }

            if (in_array(401, [$pengResp->status(), $mhsResp->status(), $katResp->status()], true)) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => 'Gagal memuat data pengajuan/mahasiswa/kategori']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        try {
            // Gunakan spoofing _method=PUT
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/{$id}?_method=PUT", $validated);

            if ($response->successful()) {
                return redirect()
                    ->route('superadmin.pengajuan.index')
                    ->with('success', 'Data pengajuan berhasil diperbarui');
            }

            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Mahasiswa tersebut sudah memiliki pengajuan.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                $first = is_array($apiErrors) ? head(head($apiErrors)) : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $msg = $response->json('message') ?? 'Gagal memperbarui data pengajuan';
            return back()->withInput()->withErrors(['error' => $msg]);

        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()
                    ->route('superadmin.pengajuan.index')
                    ->with('success', 'Data pengajuan berhasil dihapus');
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $msg = $response->json('message') ?? 'Gagal menghapus data pengajuan';
            return back()->withErrors(['error' => $msg]);

        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }
}
