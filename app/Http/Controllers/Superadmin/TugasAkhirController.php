<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TugasAkhirController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function index()
    {
        try {
            $response = Http::get($this->baseUrl . '/tugas-akhir');
            if ($response->successful()) {
                $data = $response->json('data');
                return view('superadmin.tugas_akhir.index', compact('data'));
            }
            return back()->with('error', 'Gagal mengambil data tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $mahasiswa = Http::get('http://127.0.0.1:8000/api' . '/mahasiswa')->json('data');
            return view('superadmin.tugas_akhir.create', compact('mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required',
            'kategori' => 'required|string',
            'judul' => 'required|string',
            'file_halaman_dpn' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_lembar_pengesahan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            $multipart = [
                [
                    'name' => 'id_mahasiswa',
                    'contents' => $validated['id_mahasiswa'],
                ],
                [
                    'name' => 'kategori',
                    'contents' => $validated['kategori'],
                ],
                [
                    'name' => 'judul',
                    'contents' => $validated['judul'],
                ],
            ];

            if ($request->hasFile('file_halaman_dpn')) {
                $multipart[] = [
                    'name' => 'file_halaman_dpn',
                    'contents' => fopen($request->file('file_halaman_dpn')->getPathname(), 'r'),
                    'filename' => $request->file('file_halaman_dpn')->getClientOriginalName(),
                ];
            }

            if ($request->hasFile('file_lembar_pengesahan')) {
                $multipart[] = [
                    'name' => 'file_lembar_pengesahan',
                    'contents' => fopen($request->file('file_lembar_pengesahan')->getPathname(), 'r'),
                    'filename' => $request->file('file_lembar_pengesahan')->getClientOriginalName(),
                ];
            }

            $response = Http::attach($multipart)->post($this->baseUrl . '/tugas-akhir', []);

            if ($response->successful()) {
                return redirect()->route('superadmin.tugas_akhir.index')->with('success', 'Tugas akhir berhasil ditambahkan');
            }
            return back()->with('error', 'Gagal menambahkan tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $ta = Http::get("{$this->baseUrl}/tugas-akhir/{$id}")->json('data');
            $mahasiswa = Http::get('http://127.0.0.1:8000/api' . '/mahasiswa')->json('data');
            return view('superadmin.tugas_akhir.edit', compact('ta', 'mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required',
            'kategori' => 'required|string',
            'judul' => 'required|string',
            'file_halaman_dpn' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_lembar_pengesahan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            $multipart = [
                [
                    'name' => 'id_mahasiswa',
                    'contents' => $validated['id_mahasiswa'],
                ],
                [
                    'name' => 'kategori',
                    'contents' => $validated['kategori'],
                ],
                [
                    'name' => 'judul',
                    'contents' => $validated['judul'],
                ],
                [
                    'name' => '_method',
                    'contents' => 'PUT',
                ]
            ];

            if ($request->hasFile('file_halaman_dpn')) {
                $multipart[] = [
                    'name' => 'file_halaman_dpn',
                    'contents' => fopen($request->file('file_halaman_dpn')->getPathname(), 'r'),
                    'filename' => $request->file('file_halaman_dpn')->getClientOriginalName(),
                ];
            }

            if ($request->hasFile('file_lembar_pengesahan')) {
                $multipart[] = [
                    'name' => 'file_lembar_pengesahan',
                    'contents' => fopen($request->file('file_lembar_pengesahan')->getPathname(), 'r'),
                    'filename' => $request->file('file_lembar_pengesahan')->getClientOriginalName(),
                ];
            }

            $response = Http::attach($multipart)->post("{$this->baseUrl}/tugas-akhir/{$id}", []);

            if ($response->successful()) {
                return redirect()->route('superadmin.tugas_akhir.index')->with('success', 'Tugas akhir berhasil diperbarui');
            }
            return back()->with('error', 'Gagal memperbarui tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/tugas-akhir/{$id}");
            if ($response->successful()) {
                return redirect()->route('superadmin.tugas_akhir.index')->with('success', 'Tugas akhir berhasil dihapus');
            }
            return back()->with('error', 'Gagal menghapus tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
