<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function index()
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa");

            if ($response->successful()) {
                $data = $response->json('data');
                return view('superadmin.mahasiswa.index', compact('data'));
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data mahasiswa']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/prodi");
            $prodiList = $response->successful() ? ($response->json('data') ?? []) : [];

            if (!$response->successful() && $response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return view('superadmin.mahasiswa.create', compact('prodiList'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'nim_mahasiswa'   => 'required|string|max:50',
            'nama_mahasiswa'  => 'required|string|max:255',
            'id_prodi'        => 'required|integer',
            'username'        => 'required|string|max:100',
            'password'        => 'required|string|min:6',
            'tgl_masuk'       => 'required|date_format:Y-m-d',
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date_format:Y-m-d',
            'no_telp'         => 'nullable|string|max:20',
            'alamat'          => 'nullable|string|max:255',
        ]);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/mahasiswa", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.mahasiswa.index')
                    ->with('success', 'Data mahasiswa berhasil ditambahkan');
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data mahasiswa']);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');

        try {
            $mahasiswaResponse = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id}");
            $prodiResponse     = Http::withToken($token)->get("{$this->baseUrl}/prodi");

            if ($mahasiswaResponse->successful() && $prodiResponse->successful()) {
                $mahasiswa = $mahasiswaResponse->json('data');
                $prodiList = $prodiResponse->json('data');
                return view('superadmin.mahasiswa.edit', compact('mahasiswa', 'prodiList'));
            }

            if ($mahasiswaResponse->status() === 401 || $prodiResponse->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => 'Gagal mengambil data mahasiswa atau prodi']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');

        $validated = $request->validate([
            'nim_mahasiswa'   => 'required|string|max:50',
            'nama_mahasiswa'  => 'required|string|max:255',
            'id_prodi'        => 'required|integer',
            'username'        => 'required|string|max:100',
            'password'        => 'nullable|string|min:6',
            'tgl_masuk'       => 'required|date_format:Y-m-d',
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date_format:Y-m-d',
            'no_telp'         => 'nullable|string|max:20',
            'alamat'          => 'nullable|string|max:255',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']); // jangan kirim kalau tidak diganti
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/mahasiswa/{$id}?_method=PUT", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.mahasiswa.index')
                    ->with('success', 'Data mahasiswa berhasil diperbarui');
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data mahasiswa']);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/mahasiswa/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('superadmin.mahasiswa.index')
                    ->with('success', 'Data mahasiswa berhasil dihapus');
            }

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus data mahasiswa']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Download template import mahasiswa dari API, dengan forward header yang sesuai.
     * Route: GET /superadmin/mahasiswa/import-template
     */
    public function template()
    {
        $token = Session::get('token');

        try {
            $resp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa-import-template");

            if (!$resp->successful()) {
                if ($resp->status() === 401) {
                    return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
                }
                return back()->withErrors(['error' => $resp->json('message') ?? 'Gagal mengambil template import']);
            }

            // Coba ambil nama file dari header Content-Disposition
            $disp = $resp->header('Content-Disposition');
            $filename = 'template_mahasiswa.csv';
            if ($disp && Str::contains($disp, 'filename=')) {
                $parts = explode('filename=', $disp);
                $filename = trim(trim($parts[1]), '";\' ');
            }

            return response($resp->body(), 200, [
                'Content-Type'        => $resp->header('Content-Type') ?: 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Import file CSV/XLSX ke API.
     * Route: POST /superadmin/mahasiswa/import
     */
    public function import(Request $request)
    {
        $token = Session::get('token');

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048', // 2 MB
        ], [
            'file.mimes' => 'Format file harus CSV atau Excel (.csv, .xlsx, .xls).',
            'file.max'   => 'Ukuran file maksimal 2 MB.',
        ]);

        try {
            $file = $request->file('file');

            $resp = Http::withToken($token)
                ->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post("{$this->baseUrl}/mahasiswa-import");

            if ($resp->successful()) {
                // Tampilkan ringkasan jika tersedia
                $created = data_get($resp->json(), 'created');
                $updated = data_get($resp->json(), 'updated');

                if (!is_null($created) || !is_null($updated)) {
                    return redirect()->route('superadmin.mahasiswa.index')
                        ->with('success', "Import selesai, tambah: " . ($created ?? 0) . ", perbarui: " . ($updated ?? 0));
                }

                return redirect()->route('superadmin.mahasiswa.index')
                    ->with('success', 'Import mahasiswa berhasil diproses');
            }

            if ($resp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            $msg = $resp->json('message') ?? 'Gagal import mahasiswa';
            $errors = $resp->json('errors') ?? $resp->json('data.errors') ?? null;
            if ($errors && is_array($errors)) {
                $msg .= ' (beberapa baris gagal diproses)';
            }

            return back()->withErrors(['error' => $msg]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
