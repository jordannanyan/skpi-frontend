<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiIsiCapaianController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->baseUrl}/isi-capaian");
        $data = $response->successful() ? $response->json('data') : [];
        return view('prodi.isi_capaian.index', compact('data'));
    }

    public function create()
    {
        $cplSkor = Http::get("{$this->baseUrl}/cpl-skor");
        $cplSkorList = $cplSkor->successful() ? $cplSkor->json('data') : [];
        return view('prodi.isi_capaian.create', compact('cplSkorList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cpl_skor' => 'required|numeric',
            'deskripsi_indo' => 'required|string',
            'deskripsi_inggris' => 'required|string',
        ]);

        $response = Http::post("{$this->baseUrl}/isi-capaian", $validated);

        return $response->successful()
            ? redirect()->route('prodi.isi_capaian.index')->with('success', 'Isi Capaian berhasil ditambahkan')
            : back()->with('error', 'Gagal menambahkan data');
    }

    public function edit($id)
    {
        $cplSkorList = Http::get("{$this->baseUrl}/cpl-skor")->json('data');
        $response = Http::get("{$this->baseUrl}/isi-capaian/{$id}");

        if ($response->successful()) {
            $isiCapaian = $response->json('data');
            return view('prodi.isi_capaian.edit', compact('isiCapaian', 'cplSkorList'));
        } else {
            return back()->with('error', 'Gagal mengambil data');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_cpl_skor' => 'required|numeric',
            'deskripsi_indo' => 'required|string',
            'deskripsi_inggris' => 'required|string',
        ]);

        $response = Http::post("{$this->baseUrl}/isi-capaian/{$id}?_method=PUT", $validated);

        return $response->successful()
            ? redirect()->route('prodi.isi_capaian.index')->with('success', 'Isi Capaian berhasil diperbarui')
            : back()->with('error', 'Gagal memperbarui data');
    }

    public function destroy($id)
    {
        $response = Http::post("{$this->baseUrl}/isi-capaian/{$id}?_method=DELETE");

        return $response->successful()
            ? redirect()->route('prodi.isi_capaian.index')->with('success', 'Data berhasil dihapus')
            : back()->with('error', 'Gagal menghapus data');
    }
}
