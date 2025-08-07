<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FakultasSertifikasiController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function index()
    {
        try {
            $response = Http::get("{$this->baseUrl}/sertifikasi");
            if ($response->successful()) {
                $data = $response->json('data');
                return view('fakultas.sertifikasi.index', compact('data'));
            }
            return back()->with('error', 'Gagal mengambil data sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $mahasiswa = Http::get("{$this->baseUrl}/mahasiswa")->json('data');
            return view('fakultas.sertifikasi.create', compact('mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required',
            'nama_sertifikasi' => 'required|string',
            'kategori_sertifikasi' => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            $multipart = [
                [
                    'name' => 'id_mahasiswa',
                    'contents' => $validated['id_mahasiswa'],
                ],
                [
                    'name' => 'nama_sertifikasi',
                    'contents' => $validated['nama_sertifikasi'],
                ],
                [
                    'name' => 'kategori_sertifikasi',
                    'contents' => $validated['kategori_sertifikasi'],
                ],
            ];

            if ($request->hasFile('file_sertifikat')) {
                $multipart[] = [
                    'name' => 'file_sertifikat',
                    'contents' => fopen($request->file('file_sertifikat')->getPathname(), 'r'),
                    'filename' => $request->file('file_sertifikat')->getClientOriginalName(),
                ];
            }

            $response = Http::attach($multipart)->post("{$this->baseUrl}/sertifikasi", []);

            if ($response->successful()) {
                return redirect()->route('fakultas.sertifikasi.index')->with('success', 'Sertifikasi berhasil ditambahkan');
            }
            return back()->with('error', 'Gagal menambahkan sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $sertifikasi = Http::get("{$this->baseUrl}/sertifikasi/{$id}")->json('data');
            $mahasiswa = Http::get("{$this->baseUrl}/mahasiswa")->json('data');
            return view('fakultas.sertifikasi.edit', compact('sertifikasi', 'mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required',
            'nama_sertifikasi' => 'required|string',
            'kategori_sertifikasi' => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            $multipart = [
                [
                    'name' => 'id_mahasiswa',
                    'contents' => $validated['id_mahasiswa'],
                ],
                [
                    'name' => 'nama_sertifikasi',
                    'contents' => $validated['nama_sertifikasi'],
                ],
                [
                    'name' => 'kategori_sertifikasi',
                    'contents' => $validated['kategori_sertifikasi'],
                ],
                [
                    'name' => '_method',
                    'contents' => 'PUT',
                ],
            ];

            if ($request->hasFile('file_sertifikat')) {
                $multipart[] = [
                    'name' => 'file_sertifikat',
                    'contents' => fopen($request->file('file_sertifikat')->getPathname(), 'r'),
                    'filename' => $request->file('file_sertifikat')->getClientOriginalName(),
                ];
            }

            $response = Http::attach($multipart)->post("{$this->baseUrl}/sertifikasi/{$id}", []);

            if ($response->successful()) {
                return redirect()->route('fakultas.sertifikasi.index')->with('success', 'Sertifikasi berhasil diperbarui');
            }
            return back()->with('error', 'Gagal memperbarui sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/sertifikasi/{$id}");
            if ($response->successful()) {
                return redirect()->route('fakultas.sertifikasi.index')->with('success', 'Sertifikasi berhasil dihapus');
            }
            return back()->with('error', 'Gagal menghapus sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
