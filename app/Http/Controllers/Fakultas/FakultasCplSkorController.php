<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class FakultasCplSkorController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_URL', 'http://127.0.0.1:8000/api');
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
        if (!$fakultasId) return back()->withErrors('ID Fakultas tidak ditemukan di sesi.');

        $response = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login ulang.');
        }

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return view('fakultas.cpl_skor.index', compact('data'));
        } else {
            return back()->withErrors($response->json('message') ?? 'Gagal mengambil data CPL Skor');
        }
    }

    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors('ID Fakultas tidak ditemukan di sesi.');

        $cplResp = Http::withToken($token)->get("{$this->baseUrl}/cpl", [
            'id_fakultas' => $fakultasId,
        ]);
        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($cplResp->status() === 401 || $mhsResp->status() === 401) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login ulang.');
        }

        $cpl       = $cplResp->successful() ? ($cplResp->json('data') ?? []) : [];
        $mahasiswa = $mhsResp->successful() ? ($mhsResp->json('data') ?? []) : [];

        return view('fakultas.cpl_skor.create', compact('cpl', 'mahasiswa'));
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors('ID Fakultas tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_cpl'       => 'required|numeric',
            'id_mahasiswa' => 'required|numeric',
            'skor_cpl'     => 'required|numeric',
        ]);

        $payload  = $validated + ['id_fakultas' => $fakultasId];
        $response = Http::withToken($token)->post("{$this->baseUrl}/cpl-skor", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login ulang.');
        }

        return $response->successful()
            ? redirect()->route('fakultas.cpl_skor.index')->with('success', 'CPL Skor berhasil ditambahkan')
            : back()->withErrors($response->json('message') ?? 'Gagal menambahkan data');
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors('ID Fakultas tidak ditemukan di sesi.');

        $cplResp = Http::withToken($token)->get("{$this->baseUrl}/cpl", [
            'id_fakultas' => $fakultasId,
        ]);
        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
            'id_fakultas' => $fakultasId,
        ]);
        $resp = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($resp->status() === 401 || $cplResp->status() === 401 || $mhsResp->status() === 401) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login ulang.');
        }

        if ($resp->successful()) {
            $cplSkor   = $resp->json('data') ?? [];
            $cpl       = $cplResp->successful() ? ($cplResp->json('data') ?? []) : [];
            $mahasiswa = $mhsResp->successful() ? ($mhsResp->json('data') ?? []) : [];
            return view('fakultas.cpl_skor.edit', compact('cplSkor', 'cpl', 'mahasiswa'));
        } else {
            return back()->withErrors($resp->json('message') ?? 'Gagal mengambil data');
        }
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors('ID Fakultas tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_cpl'       => 'required|numeric',
            'id_mahasiswa' => 'required|numeric',
            'skor_cpl'     => 'required|numeric',
        ]);

        $payload  = $validated + ['id_fakultas' => $fakultasId];
        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/cpl-skor/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login ulang.');
        }

        return $response->successful()
            ? redirect()->route('fakultas.cpl_skor.index')->with('success', 'Data berhasil diperbarui')
            : back()->withErrors($response->json('message') ?? 'Gagal memperbarui data');
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors('ID Fakultas tidak ditemukan di sesi.');

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/cpl-skor/{$id}?_method=DELETE", [
                'id_fakultas' => $fakultasId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login ulang.');
        }

        return $response->successful()
            ? redirect()->route('fakultas.cpl_skor.index')->with('success', 'Data berhasil dihapus')
            : back()->withErrors($response->json('message') ?? 'Gagal menghapus data');
    }
}
