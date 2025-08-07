<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class FakultasKerjaPraktekController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function index()
    {
        $token = Session::get('token');

        $response = Http::withToken($token)->get("{$this->baseUrl}/kerja-praktek");

        if ($response->successful()) {
            $data = $response->json()['data'];
            return view('fakultas.kerja_praktek.index', compact('data'));
        } else {
            return back()->withErrors(['error' => 'Gagal mengambil data kerja praktek']);
        }
    }

    public function create()
    {
        $token = Session::get('token');
        $mahasiswa = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa")->json('data');
        return view('fakultas.kerja_praktek.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required',
            'nama_kegiatan' => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $multipart = [
                [
                    'name' => 'id_mahasiswa',
                    'contents' => $validated['id_mahasiswa'],
                ],
                [
                    'name' => 'nama_kegiatan',
                    'contents' => $validated['nama_kegiatan'],
                ],
            ];

            if ($request->hasFile('file_sertifikat')) {
                $multipart[] = [
                    'name' => 'file_sertifikat',
                    'contents' => fopen($request->file('file_sertifikat')->getPathname(), 'r'),
                    'filename' => $request->file('file_sertifikat')->getClientOriginalName(),
                ];
            }

            $token = Session::get('token');
            $response = Http::withToken($token)->attach($multipart)->post("{$this->baseUrl}/kerja-praktek", []);

            if ($response->successful()) {
                return redirect()->route('fakultas.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil ditambahkan');
            } else {
                return back()->withErrors(['error' => 'Gagal menambahkan kerja praktek']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');
        $mahasiswa = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa")->json('data');
        $response = Http::withToken($token)->get("{$this->baseUrl}/kerja-praktek/{$id}");

        if ($response->successful()) {
            $kerja_praktek = $response->json('data');
            return view('fakultas.kerja_praktek.edit', compact('kerja_praktek', 'mahasiswa'));
        } else {
            return back()->withErrors(['error' => 'Gagal mengambil data kerja praktek']);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required',
            'nama_kegiatan' => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $multipart = [
                [
                    'name' => 'id_mahasiswa',
                    'contents' => $validated['id_mahasiswa'],
                ],
                [
                    'name' => 'nama_kegiatan',
                    'contents' => $validated['nama_kegiatan'],
                ],
                [
                    'name' => '_method',
                    'contents' => 'PUT',
                ]
            ];

            if ($request->hasFile('file_sertifikat')) {
                $multipart[] = [
                    'name' => 'file_sertifikat',
                    'contents' => fopen($request->file('file_sertifikat')->getPathname(), 'r'),
                    'filename' => $request->file('file_sertifikat')->getClientOriginalName(),
                ];
            }

            $token = Session::get('token');
            $response = Http::withToken($token)->attach($multipart)->post("{$this->baseUrl}/kerja-praktek/{$id}", []);

            if ($response->successful()) {
                return redirect()->route('fakultas.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil diperbarui');
            } else {
                return back()->withErrors(['error' => 'Gagal memperbarui kerja praktek']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/kerja-praktek/{$id}?_method=DELETE");

            if ($response->successful()) {
                return redirect()->route('fakultas.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil dihapus');
            } else {
                return back()->withErrors(['error' => 'Gagal menghapus kerja praktek']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
