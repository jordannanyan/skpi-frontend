<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class FakultasIsiCapaianController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
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
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $response = Http::withToken($token)->get("{$this->baseUrl}/isi-capaian", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        $data = $response->successful() ? ($response->json('data') ?? []) : [];
        return view('fakultas.isi_capaian.index', compact('data'));
    }

    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        // Ambil CPL Skor yang relevan untuk fakultas ini
        $cplSkor = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($cplSkor->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        $cplSkorList = $cplSkor->successful() ? ($cplSkor->json('data') ?? []) : [];
        return view('fakultas.isi_capaian.create', compact('cplSkorList'));
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_cpl_skor'      => 'required|numeric',
            'deskripsi_indo'   => 'required|string',
            'deskripsi_inggris'=> 'required|string',
        ]);

        $payload  = $validated + ['id_fakultas' => $fakultasId];
        $response = Http::withToken($token)->post("{$this->baseUrl}/isi-capaian", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('fakultas.isi_capaian.index')->with('success', 'Isi Capaian berhasil ditambahkan')
            : back()->withInput()->with('error', $response->json('message') ?? 'Gagal menambahkan data');
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $cplSkorListResp = Http::withToken($token)->get("{$this->baseUrl}/cpl-skor", [
            'id_fakultas' => $fakultasId,
        ]);
        $response = Http::withToken($token)->get("{$this->baseUrl}/isi-capaian/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($cplSkorListResp->status() === 401 || $response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $isiCapaian  = $response->json('data') ?? [];
            $cplSkorList = $cplSkorListResp->successful() ? ($cplSkorListResp->json('data') ?? []) : [];
            return view('fakultas.isi_capaian.edit', compact('isiCapaian', 'cplSkorList'));
        }

        return back()->with('error', $response->json('message') ?? 'Gagal mengambil data');
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_cpl_skor'      => 'required|numeric',
            'deskripsi_indo'   => 'required|string',
            'deskripsi_inggris'=> 'required|string',
        ]);

        $payload  = $validated + ['id_fakultas' => $fakultasId];
        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/isi-capaian/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('fakultas.isi_capaian.index')->with('success', 'Isi Capaian berhasil diperbarui')
            : back()->withInput()->with('error', $response->json('message') ?? 'Gagal memperbarui data');
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/isi-capaian/{$id}?_method=DELETE", [
                'id_fakultas' => $fakultasId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('fakultas.isi_capaian.index')->with('success', 'Data berhasil dihapus')
            : back()->with('error', $response->json('message') ?? 'Gagal menghapus data');
    }
}
