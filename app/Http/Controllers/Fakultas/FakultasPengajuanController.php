<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasPengajuanController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
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

        $response = Http::withToken($token)->get($this->baseUrl, [
            'id_fakultas' => $fakultasId, // /api/pengajuan?id_fakultas=...
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        $data = $response->successful() ? ($response->json('data') ?? []) : [];
        return view('fakultas.pengajuan.index', compact('data'));
    }

    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        // Ambil mahasiswa di bawah fakultas ini
        $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
            'id_fakultas' => $fakultasId,
        ]);
        // Kategori global
        $katResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');

        if ($mhsResp->status() === 401 || $katResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$mhsResp->successful() || !$katResp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat data mahasiswa atau kategori']);
        }

        $mahasiswa = $mhsResp->json('data') ?? [];
        $kategori  = $katResp->json('data') ?? [];

        return view('fakultas.pengajuan.create', compact('mahasiswa', 'kategori'));
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)->post($this->baseUrl, $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$response->successful()) {
            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data pengajuan']);
        }

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil ditambahkan');
    }

    public function show($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return redirect()->route('fakultas.pengajuan.index')
            ->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $resp = Http::withToken($token)->get("{$this->baseUrl}/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return redirect()->route('fakultas.pengajuan.index')
                ->withErrors(['error' => $resp->json('message') ?? 'Gagal memuat detail pengajuan']);
        }

        $pengajuan = $resp->json('data') ?? [];

        // Ambil data Prodi berdasarkan id_prodi dari mahasiswa
        $prodi = null;
        $idProdi = data_get($pengajuan, 'mahasiswa.id_prodi');
        if ($idProdi) {
            $prodiResp = Http::withToken($token)->get("http://127.0.0.1:8000/api/prodi/{$idProdi}");
            if ($prodiResp->successful()) {
                $prodi = $prodiResp->json('data');
            }
        }

        return view('fakultas.pengajuan.show', compact('pengajuan', 'prodi'));
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $pengResp = Http::withToken($token)->get("{$this->baseUrl}/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);
        $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
            'id_fakultas' => $fakultasId,
        ]);
        $katResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');

        if ($pengResp->status() === 401 || $mhsResp->status() === 401 || $katResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$pengResp->successful() || !$mhsResp->successful() || !$katResp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat data untuk edit pengajuan']);
        }

        $pengajuan = $pengResp->json('data') ?? [];
        $mahasiswa = $mhsResp->json('data') ?? [];
        $kategori  = $katResp->json('data') ?? [];

        return view('fakultas.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$response->successful()) {
            return back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data pengajuan']);
        }

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=DELETE", [
                'id_fakultas' => $fakultasId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$response->successful()) {
            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus data pengajuan']);
        }

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil dihapus');
    }
}
