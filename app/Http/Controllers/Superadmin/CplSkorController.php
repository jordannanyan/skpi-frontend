<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CplSkorController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->baseUrl}/cpl-skor");

        if ($response->successful()) {
            $data = $response['data'];
            return view('superadmin.cpl_skor.index', compact('data'));
        } else {
            return back()->withErrors('Gagal mengambil data CPL Skor');
        }
    }

    public function create()
    {
        $cpl = Http::get("{$this->baseUrl}/cpl")->json()['data'] ?? [];
        $mahasiswa = Http::get("{$this->baseUrl}/mahasiswa")->json()['data'] ?? [];

        return view('superadmin.cpl_skor.create', compact('cpl', 'mahasiswa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cpl' => 'required|numeric',
            'id_mahasiswa' => 'required|numeric',
            'skor_cpl' => 'required|numeric'
        ]);

        $response = Http::post("{$this->baseUrl}/cpl-skor", $validated);

        return $response->successful()
            ? redirect()->route('superadmin.cpl_skor.index')->with('success', 'CPL Skor berhasil ditambahkan')
            : back()->withErrors('Gagal menambahkan data');
    }

    public function edit($id)
    {
        $cpl = Http::get("{$this->baseUrl}/cpl")->json()['data'] ?? [];
        $mahasiswa = Http::get("{$this->baseUrl}/mahasiswa")->json()['data'] ?? [];

        $response = Http::get("{$this->baseUrl}/cpl-skor/{$id}");

        if ($response->successful()) {
            $cplSkor = $response['data'];
            return view('superadmin.cpl_skor.edit', compact('cplSkor', 'cpl', 'mahasiswa'));
        } else {
            return back()->withErrors('Gagal mengambil data');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_cpl' => 'required|numeric',
            'id_mahasiswa' => 'required|numeric',
            'skor_cpl' => 'required|numeric'
        ]);

        $response = Http::post("{$this->baseUrl}/cpl-skor/{$id}?_method=PUT", $validated);

        return $response->successful()
            ? redirect()->route('superadmin.cpl_skor.index')->with('success', 'Data berhasil diperbarui')
            : back()->withErrors('Gagal memperbarui data');
    }

    public function destroy($id)
    {
        $response = Http::post("{$this->baseUrl}/cpl-skor/{$id}?_method=DELETE");

        return $response->successful()
            ? redirect()->route('superadmin.cpl_skor.index')->with('success', 'Data berhasil dihapus')
            : back()->withErrors('Gagal menghapus data');
    }
}
