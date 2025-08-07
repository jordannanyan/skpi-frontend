<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasKategoriController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->baseUrl}/kategori");

        if ($response->successful()) {
            $data = $response->json('data');
            return view('fakultas.kategori.index', compact('data'));
        }

        return back()->withErrors('Gagal mengambil data kategori.');
    }

    public function create()
    {
        return view('fakultas.kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string',
            'status' => 'required|in:selesai,proses,batal'
        ]);

        $response = Http::post("{$this->baseUrl}/kategori", $validated);

        return $response->successful()
            ? redirect()->route('fakultas.kategori.index')->with('success', 'Kategori berhasil ditambahkan.')
            : back()->withErrors('Gagal menambahkan kategori.');
    }

    public function edit($id)
    {
        $response = Http::get("{$this->baseUrl}/kategori/{$id}");

        if ($response->successful()) {
            $kategori = $response->json('data');
            return view('fakultas.kategori.edit', compact('kategori'));
        }

        return back()->withErrors('Gagal mengambil data kategori.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string',
            'status' => 'required|in:selesai,proses,batal'
        ]);

        $response = Http::asForm()->post("{$this->baseUrl}/kategori/{$id}?_method=PUT", $validated);

        return $response->successful()
            ? redirect()->route('fakultas.kategori.index')->with('success', 'Kategori berhasil diperbarui.')
            : back()->withErrors('Gagal memperbarui kategori.');
    }

    public function destroy($id)
    {
        $response = Http::asForm()->post("{$this->baseUrl}/kategori/{$id}?_method=DELETE");

        return $response->successful()
            ? redirect()->route('fakultas.kategori.index')->with('success', 'Kategori berhasil dihapus.')
            : back()->withErrors('Gagal menghapus kategori.');
    }
}
