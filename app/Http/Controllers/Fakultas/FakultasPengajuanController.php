<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasPengajuanController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
    }

    public function index()
    {
        $response = Http::get($this->baseUrl);
        $data = $response->json('data');
        return view('fakultas.pengajuan.index', compact('data'));
    }

    public function create()
    {
        $mahasiswa = Http::get('http://127.0.0.1:8000/api/mahasiswa')->json('data');
        $kategori = Http::get('http://127.0.0.1:8000/api/kategori')->json('data');

        return view('fakultas.pengajuan.create', compact('mahasiswa', 'kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required|numeric',
            'id_kategori' => 'required|numeric',
            'status' => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date'
        ]);

        $response = Http::post($this->baseUrl, $validated);

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil ditambahkan');
    }

    public function show($id)
    {
        // jika butuh token: ->withToken(Session::get('token'))
        $resp = Http::get("{$this->baseUrl}/{$id}");
        if (!$resp->successful()) {
            return redirect()->route('fakultas.pengajuan.index')
                ->withErrors(['error' => 'Gagal memuat detail pengajuan']);
        }

        $pengajuan = $resp->json('data') ?? [];

        // Ambil data Prodi berdasarkan id_prodi dari mahasiswa
        $prodi = null;
        $idProdi = data_get($pengajuan, 'mahasiswa.id_prodi');
        if ($idProdi) {
            $prodiResp = Http::get("http://127.0.0.1:8000/api/prodi/{$idProdi}");
            if ($prodiResp->successful()) {
                $prodi = $prodiResp->json('data');
            }
        }

        return view('fakultas.pengajuan.show', compact('pengajuan', 'prodi'));
    }

    public function edit($id)
    {
        $pengajuan = Http::get("{$this->baseUrl}/{$id}")->json('data');
        $mahasiswa = Http::get('http://127.0.0.1:8000/api/mahasiswa')->json('data');
        $kategori = Http::get('http://127.0.0.1:8000/api/kategori')->json('data');

        return view('fakultas.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required|numeric',
            'id_kategori' => 'required|numeric',
            'status' => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date'
        ]);

        $response = Http::asForm()->post("{$this->baseUrl}/{$id}?_method=PUT", $validated);

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $response = Http::asForm()->post("{$this->baseUrl}/{$id}?_method=DELETE");

        return redirect()->route('fakultas.pengajuan.index')->with('success', 'Data pengajuan berhasil dihapus');
    }
}
