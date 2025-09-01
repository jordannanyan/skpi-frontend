<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiController extends Controller
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
            $response = Http::withToken($token)->get("{$this->baseUrl}/prodi");

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                return view('superadmin.prodi.index', compact('data'));
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data prodi']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        $token = Session::get('token');

        try {
            $fakultasResponse = Http::withToken($token)->get("{$this->baseUrl}/fakultas");
            $fakultasList = $fakultasResponse->successful() ? ($fakultasResponse->json('data') ?? []) : [];

            if ($fakultasResponse->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return view('superadmin.prodi.create', compact('fakultasList'));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_fakultas'      => 'required|integer',
            'nama_prodi'       => 'required|string',
            'username'         => 'required|string',
            'password'         => 'required|string|min:6',
            'akreditasi'       => 'required|string',
            'sk_akre'          => 'required|string',
            'jenis_jenjang'    => 'required|string',
            'kompetensi_kerja' => 'required|string',
            'bahasa'           => 'required|string',
            'penilaian'        => 'required|string',
            'jenis_lanjutan'   => 'required|string',
            'alamat'           => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/prodi", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.prodi.index')
                    ->with('success', 'Data prodi berhasil ditambahkan');
            }

            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Prodi dengan nama tersebut sudah ada pada fakultas ini.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                $first = is_array($apiErrors)
                    ? (is_array(reset($apiErrors)) ? reset(reset($apiErrors)) : reset($apiErrors))
                    : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data prodi']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');

        try {
            $prodiResponse    = Http::withToken($token)->get("{$this->baseUrl}/prodi/{$id}");
            $fakultasResponse = Http::withToken($token)->get("{$this->baseUrl}/fakultas");

            if ($prodiResponse->successful() && $fakultasResponse->successful()) {
                $prodi        = $prodiResponse->json('data');
                $fakultasList = $fakultasResponse->json('data') ?? [];
                $isEdit = true;
                return view('superadmin.prodi.edit', compact('prodi', 'fakultasList', 'isEdit'));
            }

            if (in_array(401, [$prodiResponse->status(), $fakultasResponse->status()], true)) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => 'Gagal mengambil data prodi/fakultas']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'id_fakultas'      => 'required|integer',
            'nama_prodi'       => 'required|string',
            'username'         => 'required|string',
            'password'         => 'nullable|string|min:6',
            'akreditasi'       => 'required|string',
            'sk_akre'          => 'required|string',
            'jenis_jenjang'    => 'required|string',
            'kompetensi_kerja' => 'required|string',
            'bahasa'           => 'required|string',
            'penilaian'        => 'required|string',
            'jenis_lanjutan'   => 'required|string',
            'alamat'           => 'required|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        try {
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/prodi/{$id}?_method=PUT", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.prodi.index')
                    ->with('success', 'Data prodi berhasil diperbarui');
            }

            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Prodi dengan nama tersebut sudah ada pada fakultas ini.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                $first = is_array($apiErrors)
                    ? (is_array(reset($apiErrors)) ? reset(reset($apiErrors)) : reset($apiErrors))
                    : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data prodi']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/prodi/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('superadmin.prodi.index')
                    ->with('success', 'Data prodi berhasil dihapus');
            }

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus data prodi']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
