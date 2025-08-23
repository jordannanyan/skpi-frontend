<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ProdiMahasiswaController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    /** Get id_prodi from session->id (set at login for role=prodi) */
    private function getProdiId(): ?int
    {
        if (Session::get('role') !== 'prodi') return null;
        $id = Session::get('id'); // this is id_prodi for role=prodi
        return is_numeric($id) ? (int) $id : null;
    }

    public function index()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        try {
            // => GET /mahasiswa?id_prodi=1
            $response = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
                'id_prodi' => $prodiId
            ]);

            if ($response->successful()) {
                $data = $response->json('data');
                return view('prodi.mahasiswa.index', compact('data'));
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
            // opsional: kalau form tidak butuh dropdown prodi, ini bisa dihapus
            $response = Http::withToken($token)->get("{$this->baseUrl}/prodi");
            $prodiList = $response->successful() ? ($response->json('data') ?? []) : [];

            if (!$response->successful() && $response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            return view('prodi.mahasiswa.create', compact('prodiList'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'nim_mahasiswa'   => 'required|string|max:50',
            'nama_mahasiswa'  => 'required|string|max:255',
            'username'        => 'required|string|max:100',
            'password'        => 'required|string|min:6',
            'tgl_masuk'       => 'required|date_format:Y-m-d',
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date_format:Y-m-d',
            'no_telp'         => 'nullable|string|max:20',
            'alamat'          => 'nullable|string|max:255',
        ]);

        $payload = $validated + ['id_prodi' => $prodiId];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/mahasiswa", $payload);

            if ($response->successful()) {
                return redirect()->route('prodi.mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan');
            }
            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data mahasiswa']);
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
                return view('prodi.mahasiswa.edit', compact('mahasiswa', 'prodiList'));
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
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'nim_mahasiswa'   => 'required|string|max:50',
            'nama_mahasiswa'  => 'required|string|max:255',
            'username'        => 'required|string|max:100',
            'password'        => 'nullable|string|min:6',
            'tgl_masuk'       => 'required|date_format:Y-m-d',
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date_format:Y-m-d',
            'no_telp'         => 'nullable|string|max:20',
            'alamat'          => 'nullable|string|max:255',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $payload = $validated + ['id_prodi' => $prodiId];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/mahasiswa/{$id}?_method=PUT", $payload);

            if ($response->successful()) {
                return redirect()->route('prodi.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui');
            }
            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data mahasiswa']);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/mahasiswa/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('prodi.mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus');
            }
            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus data mahasiswa']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
