<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    public function index()
    {
        $token = Session::get('token');
        $response = Http::withToken($token)->get("{$this->baseUrl}/prodi");

        if ($response->successful()) {
            $data = $response->json('data');
            return view('superadmin.prodi.index', compact('data'));
        } else {
            return back()->withErrors(['error' => 'Gagal mengambil data prodi']);
        }
    }

    public function create()
    {
        $fakultasResponse = Http::get("{$this->baseUrl}/fakultas");
        $fakultasList = $fakultasResponse->successful() ? $fakultasResponse->json('data') : [];

        return view('superadmin.prodi.create', compact('fakultasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_fakultas' => 'required|integer',
            'nama_prodi' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string|min:6',
            'akreditasi' => 'required|string',
            'sk_akre' => 'required|string',
            'jenis_jenjang' => 'required|string',
            'kompetensi_kerja' => 'required|string',
            'bahasa' => 'required|string',
            'penilaian' => 'required|string',
            'jenis_lanjutan' => 'required|string',
            'alamat' => 'required|string',
        ]);

        $response = Http::post("{$this->baseUrl}/prodi", $validated);

        return $response->successful()
            ? redirect()->route('superadmin.prodi.index')->with('success', 'Data prodi berhasil ditambahkan')
            : redirect()->back()->with('error', 'Gagal menambahkan data prodi');
    }

    public function edit($id)
    {
        $prodiResponse = Http::get("{$this->baseUrl}/prodi/{$id}");
        $fakultasResponse = Http::get("{$this->baseUrl}/fakultas");

        if ($prodiResponse->successful()) {
            $prodi = $prodiResponse->json('data');
            $fakultasList = $fakultasResponse->successful() ? $fakultasResponse->json('data') : [];
            return view('superadmin.prodi.edit', compact('prodi', 'fakultasList'));
        } else {
            return redirect()->back()->with('error', 'Gagal mengambil data prodi');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_fakultas' => 'required|integer',
            'nama_prodi' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string|min:6',
            'akreditasi' => 'required|string',
            'sk_akre' => 'required|string',
            'jenis_jenjang' => 'required|string',
            'kompetensi_kerja' => 'required|string',
            'bahasa' => 'required|string',
            'penilaian' => 'required|string',
            'jenis_lanjutan' => 'required|string',
            'alamat' => 'required|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $response = Http::post("{$this->baseUrl}/prodi/{$id}?_method=PUT", $validated);

        return $response->successful()
            ? redirect()->route('superadmin.prodi.index')->with('success', 'Data prodi berhasil diperbarui')
            : redirect()->back()->with('error', 'Gagal memperbarui data prodi');
    }

    public function destroy($id)
    {
        $response = Http::post("{$this->baseUrl}/prodi/{$id}?_method=DELETE");

        return $response->successful()
            ? redirect()->route('superadmin.prodi.index')->with('success', 'Data prodi berhasil dihapus')
            : redirect()->back()->with('error', 'Gagal menghapus data prodi');
    }
}
