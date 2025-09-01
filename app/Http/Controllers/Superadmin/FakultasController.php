<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api'; // ganti jika base API berubah
    }

    public function index()
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/fakultas");

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                return view('superadmin.fakultas.index', compact('data'));
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data fakultas']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function create()
    {
        return view('superadmin.fakultas.create');
    }

    public function store(Request $request)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'nama_fakultas'  => 'required|string|max:255',
            'username'       => 'required|string|max:255',
            'password'       => 'required|string|min:6',
            'nama_dekan'     => 'required|string|max:255',
            'nip'            => 'required|string|max:20',
            'alamat'         => 'required|string',
        ]);

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/fakultas", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.fakultas.index')->with('success', 'Fakultas berhasil ditambahkan');
            }

            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Akun Fakultas untuk nama tersebut sudah ada.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                // ambil pesan pertama jika ada
                $first = is_array($apiErrors) ? (is_array(reset($apiErrors)) ? reset(reset($apiErrors)) : reset($apiErrors)) : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan fakultas']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$id}");

            if ($response->successful()) {
                $fakultas = $response->json('data');
                return view('superadmin.fakultas.edit', compact('fakultas'));
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data fakultas']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'nama_fakultas'  => 'required|string|max:255',
            'username'       => 'required|string|max:255',
            'password'       => 'nullable|string|min:6',
            'nama_dekan'     => 'required|string|max:255',
            'nip'            => 'required|string|max:255',
            'alamat'         => 'required|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        try {
            // spoofing PUT
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/fakultas/{$id}?_method=PUT", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.fakultas.index')->with('success', 'Data fakultas berhasil diperbarui');
            }

            if ($response->status() === 409) {
                $msg = $response->json('message') ?? 'Akun Fakultas untuk nama tersebut sudah ada.';
                return back()->withInput()->withErrors(['error' => $msg]);
            }

            if ($response->status() === 422) {
                $apiErrors = $response->json('errors') ?? [];
                $first = is_array($apiErrors) ? (is_array(reset($apiErrors)) ? reset(reset($apiErrors)) : reset($apiErrors)) : null;
                return back()->withInput()->withErrors(['error' => $first ?: 'Validasi API gagal.']);
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data fakultas']);
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
                ->post("{$this->baseUrl}/fakultas/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('superadmin.fakultas.index')->with('success', 'Data fakultas berhasil dihapus');
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus fakultas']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }
}
