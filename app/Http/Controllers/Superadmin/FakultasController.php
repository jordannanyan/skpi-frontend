<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api'; // ganti jika base API berubah
    }

    public function index()
    {
        $token = Session::get('token');

        $response = Http::withToken($token)->get("{$this->baseUrl}/fakultas");

        if ($response->successful()) {
            $data = $response->json()['data'];
            return view('superadmin.fakultas.index', compact('data'));
        } else {
            return back()->withErrors(['error' => 'Gagal mengambil data fakultas']);
        }
    }

    public function create()
    {
        return view('superadmin.fakultas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_fakultas' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'nama_dekan' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/fakultas", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.fakultas.index')->with('success', 'Fakultas berhasil ditambahkan');
            } else {
                return back()->withErrors(['error' => 'Gagal menambahkan fakultas']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/fakultas/{$id}");

            if ($response->successful()) {
                $fakultas = $response->json('data');
                return view('superadmin.fakultas.edit', compact('fakultas'));
            } else {
                return back()->withErrors(['error' => 'Gagal mengambil data fakultas']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_fakultas' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
            'nama_dekan' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
            'alamat' => 'required|string',
        ]);

        // Hapus password jika tidak diisi
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        try {
            $response = Http::post("{$this->baseUrl}/fakultas/{$id}?_method=PUT", $validated);

            if ($response->successful()) {
                return redirect()->route('superadmin.fakultas.index')->with('success', 'Data fakultas berhasil diperbarui');
            } else {
                return redirect()->back()->with('error', 'Gagal memperbarui data fakultas');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $response = Http::post("{$this->baseUrl}/fakultas/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('superadmin.fakultas.index')->with('success', 'Data fakultas berhasil dihapus');
            } else {
                return back()->withErrors(['error' => 'Gagal menghapus fakultas']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
