<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function index()
    {
        $token = Session::get('token');
        $response = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa");

        if ($response->successful()) {
            $data = $response->json()['data'];
            return view('superadmin.mahasiswa.index', compact('data'));
        } else {
            return back()->withErrors(['error' => 'Gagal mengambil data mahasiswa']);
        }
    }

    public function create()
    {
        $token = Session::get('token');
        $response = Http::withToken($token)->get("{$this->baseUrl}/prodi");

        $prodiList = $response->successful() ? $response->json()['data'] : [];

        return view('superadmin.mahasiswa.create', compact('prodiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mahasiswa' => 'required|string|max:255',
            'nim_mahasiswa' => 'required|string|max:255',
            'id_prodi' => 'required|numeric',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'no_telp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/mahasiswa", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menambahkan data mahasiswa')->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $token = Session::get('token'); // jika dibutuhkan

            // Ambil data mahasiswa berdasarkan ID
            $mahasiswaResponse = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id}");

            // Ambil seluruh daftar prodi untuk dropdown
            $prodiResponse = Http::withToken($token)->get("{$this->baseUrl}/prodi");

            if ($mahasiswaResponse->successful() && $prodiResponse->successful()) {
                $mahasiswa = $mahasiswaResponse->json('data');
                $prodiList = $prodiResponse->json('data');

                return view('superadmin.mahasiswa.edit', compact('mahasiswa', 'prodiList'));
            } else {
                return redirect()->back()->with('error', 'Gagal mengambil data mahasiswa atau prodi');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_mahasiswa' => 'required|string|max:255',
            'nim_mahasiswa' => 'required|string|max:255',
            'id_prodi' => 'required|numeric',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'no_telp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/mahasiswa/{$id}?_method=PUT", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui');
            } else {
                return redirect()->back()->with('error', 'Gagal memperbarui data mahasiswa');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $response = Http::post("{$this->baseUrl}/mahasiswa/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('superadmin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus data mahasiswa');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
