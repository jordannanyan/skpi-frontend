<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PengesahanController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function index()
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/pengesahan");

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                return view('superadmin.pengesahan.index', compact('data'));
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data pengesahan']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function create()
    {
        $token = Session::get('token');

        try {
            $fakResp = Http::withToken($token)->get("{$this->baseUrl}/fakultas");
            $pengResp = Http::withToken($token)->get("{$this->baseUrl}/pengajuan");

            if ($fakResp->successful() && $pengResp->successful()) {
                $fakultas = $fakResp->json('data') ?? [];
                $pengajuan = $pengResp->json('data') ?? [];
                return view('superadmin.pengesahan.create', compact('fakultas', 'pengajuan'));
            }

            if ($fakResp->status() === 401 || $pengResp->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => 'Gagal memuat data fakultas/pengajuan']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_fakultas'      => 'required|numeric',
            'id_pengajuan'     => 'required|numeric',
            'tgl_pengesahan'   => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/pengesahan", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.pengesahan.index')
                    ->with('success', 'Data berhasil ditambahkan');
            }

            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Pengesahan sudah ada untuk pengajuan ini.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                $first = is_array($apiErrors) ? head(head($apiErrors)) : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $msg = $response->json('message') ?? 'Gagal menambahkan data';
            return back()->withInput()->withErrors(['error' => $msg]);

        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');

        try {
            $penResp = Http::withToken($token)->get("{$this->baseUrl}/pengesahan/{$id}");
            $fakResp = Http::withToken($token)->get("{$this->baseUrl}/fakultas");
            $pengResp = Http::withToken($token)->get("{$this->baseUrl}/pengajuan");

            if ($penResp->successful() && $fakResp->successful() && $pengResp->successful()) {
                $pengesahan = $penResp->json('data') ?? null;
                $fakultas = $fakResp->json('data') ?? [];
                $pengajuan = $pengResp->json('data') ?? [];
                return view('superadmin.pengesahan.edit', compact('pengesahan', 'fakultas', 'pengajuan'));
            }

            if (in_array(401, [$penResp->status(), $fakResp->status(), $pengResp->status()], true)) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => 'Gagal memuat data pengesahan/fakultas/pengajuan']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_fakultas'      => 'required|numeric',
            'id_pengajuan'     => 'required|numeric',
            'tgl_pengesahan'   => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        try {
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/pengesahan/{$id}?&_method=PUT", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.pengesahan.index')
                    ->with('success', 'Data berhasil diperbarui');
            }

            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Pengesahan untuk pengajuan tersebut sudah ada.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                $first = is_array($apiErrors) ? head(head($apiErrors)) : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $msg = $response->json('message') ?? 'Gagal memperbarui data';
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
                ->post("{$this->baseUrl}/pengesahan/{$id}?&_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('superadmin.pengesahan.index')
                    ->with('success', 'Data berhasil dihapus');
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $msg = $response->json('message') ?? 'Gagal menghapus data';
            return back()->withErrors(['error' => $msg]);

        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function print($id)
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/pengesahan/print/{$id}");

            if ($response->successful()) {
                $data = $response->json('data');
                $cpl_data = $response->json('cpl_data');
                return view('superadmin.pengesahan.print', compact('data', 'cpl_data'));
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data print']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }
}
