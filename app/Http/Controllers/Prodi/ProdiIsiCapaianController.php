<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiIsiCapaianController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
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

        $response = Http::withToken($token)->get("{$this->baseUrl}/isi-capaian", [
            'id_prodi' => $prodiId, // => /isi-capaian?id_prodi=1
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        $data = $response->successful() ? ($response->json('data') ?? []) : [];
        return view('prodi.isi_capaian.index', compact('data'));
    }

    public function create()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        // Ambil daftar CPL Skor milik prodi ini saja
        $cplSkorResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
            'id_prodi' => $prodiId,
        ]);

        if ($cplSkorResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$cplSkorResp->successful()) {
            return back()->withErrors(['error' => 'Gagal mengambil data CPL Skor']);
        }

        $cplSkorList = $cplSkorResp->json('data') ?? [];
        return view('prodi.isi_capaian.create', compact('cplSkorList'));
    }

    public function store(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_cpl_skor'       => 'required|numeric',
            'deskripsi_indo'    => 'required|string',
            'deskripsi_inggris' => 'required|string',
        ]);

        $payload = $validated + ['id_prodi' => $prodiId];

        $response = Http::withToken($token)->post("{$this->baseUrl}/isi-capaian", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('prodi.isi_capaian.index')->with('success', 'Isi Capaian berhasil ditambahkan')
            : back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data']);
    }

    public function edit($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $cplSkorResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
            'id_prodi' => $prodiId,
        ]);
        $isiResp = Http::withToken($token)->get("{$this->baseUrl}/isi-capaian/{$id}", [
            'id_prodi' => $prodiId,
        ]);

        if ($cplSkorResp->status() === 401 || $isiResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$cplSkorResp->successful() || !$isiResp->successful()) {
            return back()->withErrors(['error' => 'Gagal mengambil data untuk edit']);
        }

        $cplSkorList = $cplSkorResp->json('data') ?? [];
        $isiCapaian  = $isiResp->json('data') ?? [];

        return view('prodi.isi_capaian.edit', compact('isiCapaian', 'cplSkorList'));
    }

    public function update(Request $request, $id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_cpl_skor'       => 'required|numeric',
            'deskripsi_indo'    => 'required|string',
            'deskripsi_inggris' => 'required|string',
        ]);

        $payload = $validated + ['id_prodi' => $prodiId];

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/isi-capaian/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('prodi.isi_capaian.index')->with('success', 'Isi Capaian berhasil diperbarui')
            : back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data']);
    }

    public function destroy($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/isi-capaian/{$id}?_method=DELETE", [
                'id_prodi' => $prodiId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('prodi.isi_capaian.index')->with('success', 'Data berhasil dihapus')
            : back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus data']);
    }
}
