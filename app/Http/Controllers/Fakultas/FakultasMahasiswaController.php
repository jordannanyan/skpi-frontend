<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class FakultasMahasiswaController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    /** Ambil id_fakultas dari session->id saat role=fakultas */
    private function getFakultasId(): ?int
    {
        if (Session::get('role') !== 'fakultas') return null;
        $id = Session::get('id'); // diset saat login sebagai id_fakultas
        return is_numeric($id) ? (int) $id : null;
    }

    public function index()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $response = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
            'id_fakultas' => $fakultasId, // => /mahasiswa?id_fakultas=...
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return view('fakultas.mahasiswa.index', compact('data'));
        }

        return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data mahasiswa']);
    }

    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        // Ambil prodi yang berada di bawah fakultas ini saja
        $response = Http::withToken($token)->get("{$this->baseUrl}/prodi", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        $prodiList = $response->successful() ? ($response->json('data') ?? []) : [];
        return view('fakultas.mahasiswa.create', compact('prodiList'));
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'nama_mahasiswa' => 'required|string|max:255',
            'nim_mahasiswa'  => 'required|string|max:255',
            'id_prodi'       => 'required|numeric',
            'tempat_lahir'   => 'required|string',
            'tanggal_lahir'  => 'required|date',
            'no_telp'        => 'nullable|string',
            'alamat'         => 'nullable|string',
        ]);

        // Sertakan id_fakultas dalam body (server bisa validasi otorisasi)
        $payload = $validated + ['id_fakultas' => $fakultasId];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/mahasiswa", $payload);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('fakultas.mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan');
            }
            return redirect()->back()->with('error', $response->json('message') ?? 'Gagal menambahkan data mahasiswa')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        try {
            // Ambil data mahasiswa (sertakan id_fakultas sebagai query untuk otorisasi/filter)
            $mahasiswaResponse = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id}", [
                'id_fakultas' => $fakultasId,
            ]);

            // Ambil daftar prodi di bawah fakultas ini
            $prodiResponse = Http::withToken($token)->get("{$this->baseUrl}/prodi", [
                'id_fakultas' => $fakultasId,
            ]);

            if ($mahasiswaResponse->status() === 401 || $prodiResponse->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($mahasiswaResponse->successful() && $prodiResponse->successful()) {
                $mahasiswa = $mahasiswaResponse->json('data') ?? [];
                $prodiList = $prodiResponse->json('data') ?? [];
                return view('fakultas.mahasiswa.edit', compact('mahasiswa', 'prodiList'));
            }

            return redirect()->back()->with('error', 'Gagal mengambil data mahasiswa atau prodi');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'nama_mahasiswa' => 'required|string|max:255',
            'nim_mahasiswa'  => 'required|string|max:255',
            'id_prodi'       => 'required|numeric',
            'tempat_lahir'   => 'required|string',
            'tanggal_lahir'  => 'required|date',
            'no_telp'        => 'nullable|string',
            'alamat'         => 'nullable|string',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        try {
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/mahasiswa/{$id}?_method=PUT", $payload);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('fakultas.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui');
            }
            return redirect()->back()->with('error', $response->json('message') ?? 'Gagal memperbarui data mahasiswa');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        try {
            // Gunakan method override supaya id_fakultas bisa dikirim di body
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/mahasiswa/{$id}?_method=DELETE", [
                    'id_fakultas' => $fakultasId,
                ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('fakultas.mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus');
            }
            return redirect()->back()->with('error', $response->json('message') ?? 'Gagal menghapus data mahasiswa');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
