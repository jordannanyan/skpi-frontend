<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasPengesahanController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function index()
    {
        $response = Http::get("{$this->baseUrl}/pengesahan");

        if ($response->successful()) {
            $data = $response->json('data');
            return view('fakultas.pengesahan.index', compact('data'));
        }

        return back()->with('error', 'Gagal mengambil data pengesahan');
    }

    public function create()
    {
        $fakultas = Http::get("{$this->baseUrl}/fakultas")->json('data') ?? [];
        $pengajuan = Http::get("{$this->baseUrl}/pengajuan")->json('data') ?? [];

        return view('fakultas.pengesahan.create', compact('fakultas', 'pengajuan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_fakultas' => 'required|numeric',
            'id_pengajuan' => 'required|numeric',
            'tgl_pengesahan' => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        $response = Http::post("{$this->baseUrl}/pengesahan", $validated);

        if ($response->successful()) {
            return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil ditambahkan');
        }

        return back()->with('error', 'Gagal menambahkan data');
    }

    public function edit($id)
    {
        $pengesahan = Http::get("{$this->baseUrl}/pengesahan/{$id}")->json('data') ?? null;
        $fakultas = Http::get("{$this->baseUrl}/fakultas")->json('data') ?? [];
        $pengajuan = Http::get("{$this->baseUrl}/pengajuan")->json('data') ?? [];

        return view('fakultas.pengesahan.edit', compact('pengesahan', 'fakultas', 'pengajuan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_fakultas' => 'required|numeric',
            'id_pengajuan' => 'required|numeric',
            'tgl_pengesahan' => 'required|date',
            'nomor_pengesahan' => 'required|string',
        ]);

        $response = Http::post("{$this->baseUrl}/pengesahan/{$id}?_method=PUT", $validated);

        if ($response->successful()) {
            return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil diperbarui');
        }

        return back()->with('error', 'Gagal memperbarui data');
    }

    public function destroy($id)
    {
        $response = Http::post("{$this->baseUrl}/pengesahan/{$id}?_method=DELETE");

        if ($response->successful()) {
            return redirect()->route('fakultas.pengesahan.index')->with('success', 'Data berhasil dihapus');
        }

        return back()->with('error', 'Gagal menghapus data');
    }

    public function print($id)
    {
        try {
            $response = Http::get("http://127.0.0.1:8000/api/pengesahan/print/{$id}");

            if ($response->successful()) {
                $data = $response->json('data');
                $cpl_data = $response->json('cpl_data'); // ✅ extract this too

                return view('superadmin.pengesahan.print', compact('data', 'cpl_data'));
            }

            return back()->with('error', 'Gagal mengambil data print');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
