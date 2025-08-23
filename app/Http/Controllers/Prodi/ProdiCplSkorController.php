<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ProdiCplSkorController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_URL', 'http://127.0.0.1:8000/api');
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

        $response = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
            'id_prodi' => $prodiId, // => /cpl-skor?id_prodi=1
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return view('prodi.cpl_skor.index', compact('data'));
        }

        return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data CPL Skor']);
    }

    public function create()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $cplResp = Http::withToken($token)->get("{$this->baseUrl}/cpl", [
            'id_prodi' => $prodiId,
        ]);
        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
            'id_prodi' => $prodiId,
        ]);

        if ($cplResp->status() === 401 || $mhsResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$cplResp->successful() || !$mhsResp->successful()) {
            return back()->withErrors(['error' => 'Gagal mengambil data CPL atau Mahasiswa']);
        }

        $cpl       = $cplResp->json('data') ?? [];
        $mahasiswa = $mhsResp->json('data') ?? [];

        return view('prodi.cpl_skor.create', compact('cpl', 'mahasiswa'));
    }

    public function store(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_cpl'       => 'required|numeric',
            'id_mahasiswa' => 'required|numeric',
            'skor_cpl'     => 'required|numeric',
        ]);

        $payload = $validated + ['id_prodi' => $prodiId];

        $response = Http::withToken($token)->post("{$this->baseUrl}/cpl-skor", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('prodi.cpl_skor.index')->with('success', 'CPL Skor berhasil ditambahkan')
            : back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan data']);
    }

    public function edit($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $cplResp = Http::withToken($token)->get("{$this->baseUrl}/cpl", [
            'id_prodi' => $prodiId,
        ]);
        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
            'id_prodi' => $prodiId,
        ]);
        $response = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor/{$id}", [
            'id_prodi' => $prodiId,
        ]);

        if ($cplResp->status() === 401 || $mhsResp->status() === 401 || $response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$cplResp->successful() || !$mhsResp->successful() || !$response->successful()) {
            return back()->withErrors(['error' => 'Gagal mengambil data untuk edit']);
        }

        $cpl     = $cplResp->json('data') ?? [];
        $mahasiswa = $mhsResp->json('data') ?? [];
        $cplSkor = $response->json('data') ?? [];

        return view('prodi.cpl_skor.edit', compact('cplSkor', 'cpl', 'mahasiswa'));
    }

    public function update(Request $request, $id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withInput()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_cpl'       => 'required|numeric',
            'id_mahasiswa' => 'required|numeric',
            'skor_cpl'     => 'required|numeric',
        ]);

        $payload = $validated + ['id_prodi' => $prodiId];

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/cpl-skor/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('prodi.cpl_skor.index')->with('success', 'Data berhasil diperbarui')
            : back()->withInput()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui data']);
    }

    public function destroy($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/cpl-skor/{$id}?_method=DELETE", [
                'id_prodi' => $prodiId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('prodi.cpl_skor.index')->with('success', 'Data berhasil dihapus')
            : back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus data']);
    }
}
