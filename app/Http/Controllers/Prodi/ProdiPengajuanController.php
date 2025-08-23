<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiPengajuanController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        // keep pengajuan base
        $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
    }

    /** Get id_prodi from session->id when role=prodi */
    private function getProdiId(): ?int
    {
        if (Session::get('role') !== 'prodi') return null;
        $id = Session::get('id'); // set at login for role=prodi as id_prodi
        return is_numeric($id) ? (int) $id : null;
    }

    public function index()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $resp = Http::withToken($token)->get($this->baseUrl, [
            'id_prodi' => $prodiId, // => /api/pengajuan?id_prodi=1
        ]);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return back()->withErrors(['error' => $resp->json('message') ?? 'Gagal memuat data pengajuan']);
        }

        $data = $resp->json('data') ?? [];
        return view('prodi.pengajuan.index', compact('data'));
    }

    public function create()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        // Filter mahasiswa by id_prodi
        $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
            'id_prodi' => $prodiId, // => /api/mahasiswa?id_prodi=1
        ]);
        // kategori biasanya global (tanpa filter prodi)
        $katResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/kategori');

        if ($mhsResp->status() === 401 || $katResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$mhsResp->successful() || !$katResp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat data mahasiswa atau kategori']);
        }

        $mahasiswa = $mhsResp->json('data') ?? [];
        $kategori  = $katResp->json('data') ?? [];

        return view('prodi.pengajuan.create', compact('mahasiswa', 'kategori'));
    }

    public function show($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        // Pass id_prodi too (backend may authorize/filter by it)
        $resp = Http::withToken($token)->get("{$this->baseUrl}/{$id}", [
            'id_prodi' => $prodiId,
        ]);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return redirect()->route('prodi.pengajuan.index')
                ->withErrors(['error' => $resp->json('message') ?? 'Gagal memuat detail pengajuan']);
        }

        $pengajuan = $resp->json('data') ?? [];
        return view('prodi.pengajuan.show', compact('pengajuan'));
    }

    public function store(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        // If your API accepts id_prodi in body, include it; if not, it will still be
        // inferred from id_mahasiswa on backend.
        $payload = $validated + ['id_prodi' => $prodiId];

        $resp = Http::withToken($token)->post($this->baseUrl, $payload);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal menambahkan data pengajuan']);
        }

        return redirect()->route('prodi.pengajuan.index')->with('success', 'Data pengajuan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $pengResp = Http::withToken($token)->get("{$this->baseUrl}/{$id}", [
            'id_prodi' => $prodiId,
        ]);
        $mhsResp = Http::withToken($token)->get('http://127.0.0.1:8000/api/mahasiswa', [
            'id_prodi' => $prodiId,
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

        return view('prodi.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date',
        ]);

        $payload = $validated + ['id_prodi' => $prodiId];

        $resp = Http::withToken($token)->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=PUT", $payload);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return back()->withInput()->withErrors(['error' => $resp->json('message') ?? 'Gagal memperbarui data pengajuan']);
        }

        return redirect()->route('prodi.pengajuan.index')->with('success', 'Data pengajuan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $resp = Http::withToken($token)->asForm()
            ->post("{$this->baseUrl}/{$id}?_method=DELETE", [
                'id_prodi' => $prodiId, // pass along if your backend authorizes by prodi
            ]);

        if ($resp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$resp->successful()) {
            return back()->withErrors(['error' => $resp->json('message') ?? 'Gagal menghapus data pengajuan']);
        }

        return redirect()->route('prodi.pengajuan.index')->with('success', 'Data pengajuan berhasil dihapus');
    }
}
