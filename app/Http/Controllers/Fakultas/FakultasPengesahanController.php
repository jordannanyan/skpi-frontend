<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasPengesahanController extends Controller
{
    private string $baseUrl;

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
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $response = Http::withToken($token)->get("{$this->baseUrl}/pengesahan", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return view('fakultas.pengesahan.index', compact('data'));
        }

        return back()->with('error', $response->json('message') ?? 'Gagal mengambil data pengesahan');
    }

    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        // Ambil data fakultas sendiri (wrap jadi array supaya kompatibel dengan view yang expect list)
        $fakResp = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$fakultasId}");
        // Ambil pengajuan yang berada di bawah fakultas ini
        $pengResp = Http::withToken($token)->get("{$this->baseUrl}/pengajuan", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($fakResp->status() === 401 || $pengResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$fakResp->successful() || !$pengResp->successful()) {
            return back()->with('error', 'Gagal memuat data fakultas atau pengajuan');
        }

        $fakultas  = $fakResp->json('data') ? [ $fakResp->json('data') ] : [];
        $pengajuan = $pengResp->json('data') ?? [];

        return view('fakultas.pengesahan.create', compact('fakultas', 'pengajuan'));
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        // id_fakultas diambil dari session, bukan dari form
        $validated = $request->validate([
            'id_pengajuan'     => 'required|numeric',
            'tgl_pengesahan'   => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)->post("{$this->baseUrl}/pengesahan", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if ($response->failed()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Gagal menambahkan data');
        }

        return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $pengsResp = Http::withToken($token)->get("{$this->baseUrl}/pengesahan/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);
        $fakResp = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$fakultasId}");
        $pengResp = Http::withToken($token)->get("{$this->baseUrl}/pengajuan", [
            'id_fakultas' => $fakultasId,
        ]);

        if ($pengsResp->status() === 401 || $fakResp->status() === 401 || $pengResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$pengsResp->successful() || !$fakResp->successful() || !$pengResp->successful()) {
            return back()->with('error', 'Gagal memuat data untuk edit');
        }

        $pengesahan = $pengsResp->json('data') ?? null;
        $fakultas   = $fakResp->json('data') ? [ $fakResp->json('data') ] : [];
        $pengajuan  = $pengResp->json('data') ?? [];

        return view('fakultas.pengesahan.edit', compact('pengesahan', 'fakultas', 'pengajuan'));
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_pengajuan'     => 'required|numeric',
            'tgl_pengesahan'   => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/pengesahan/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if ($response->failed()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Gagal memperbarui data');
        }

        return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/pengesahan/{$id}?_method=DELETE", [
                'id_fakultas' => $fakultasId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal menghapus data');
        }

        return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil dihapus');
    }

    public function print($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        try {
            // Sertakan id_fakultas agar backend bisa otorisasi/filtrasi
            $response = Http::withToken($token)->get("{$this->baseUrl}/pengesahan/print/{$id}", [
                'id_fakultas' => $fakultasId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->failed()) {
                return back()->with('error', $response->json('message') ?? 'Gagal mengambil data print');
            }

            $data     = $response->json('data');
            $cpl_data = $response->json('cpl_data');

            return view('superadmin.pengesahan.print', compact('data', 'cpl_data'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
