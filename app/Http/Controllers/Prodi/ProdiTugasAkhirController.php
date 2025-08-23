<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiTugasAkhirController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    /** Get id_prodi from session->id when role=prodi */
    private function getProdiId(): ?int
    {
        if (Session::get('role') !== 'prodi') return null;
        $id = Session::get('id'); // set at login for role=prodi as id_prodi
        return is_numeric($id) ? (int) $id : null;
    }

    public function index()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->with('error', 'ID Prodi tidak ditemukan di sesi.');

        try {
            $response = Http::withToken($token)->get($this->baseUrl . '/tugas-akhir', [
                'id_prodi' => $prodiId, // => /tugas-akhir?id_prodi=1
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                $data = $response->json('data');
                return view('prodi.tugas_akhir.index', compact('data'));
            }

            return back()->with('error', $response->json('message') ?? 'Gagal mengambil data tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->with('error', 'ID Prodi tidak ditemukan di sesi.');

        try {
            // Filter mahasiswa by prodi
            $mhsResp = Http::withToken($token)->get($this->baseUrl . '/mahasiswa', [
                'id_prodi' => $prodiId,
            ]);

            if ($mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful()) {
                return back()->with('error', 'Gagal mengambil data mahasiswa');
            }

            $mahasiswa = $mhsResp->json('data');
            return view('prodi.tugas_akhir.create', compact('mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->with('error', 'ID Prodi tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_mahasiswa'           => 'required',
            'kategori'               => 'required|string',
            'judul'                  => 'required|string',
            'file_halaman_dpn'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_lembar_pengesahan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_halaman_dpn')) {
                $file = $request->file('file_halaman_dpn');
                $req = $req->attach('file_halaman_dpn', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            if ($request->hasFile('file_lembar_pengesahan')) {
                $file = $request->file('file_lembar_pengesahan');
                $req = $req->attach('file_lembar_pengesahan', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post($this->baseUrl . '/tugas-akhir', [
                'id_mahasiswa' => $validated['id_mahasiswa'],
                'kategori'     => $validated['kategori'],
                'judul'        => $validated['judul'],
                'id_prodi'     => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('prodi.tugas_akhir.index')->with('success', 'Tugas akhir berhasil ditambahkan');
            }

            return back()->with('error', $response->json('message') ?? 'Gagal menambahkan tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->with('error', 'ID Prodi tidak ditemukan di sesi.');

        try {
            $taResp = Http::withToken($token)->get("{$this->baseUrl}/tugas-akhir/{$id}", [
                'id_prodi' => $prodiId,
            ]);
            $mhsResp = Http::withToken($token)->get($this->baseUrl . '/mahasiswa', [
                'id_prodi' => $prodiId,
            ]);

            if ($taResp->status() === 401 || $mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$taResp->successful() || !$mhsResp->successful()) {
                return back()->with('error', 'Gagal memuat data edit tugas akhir');
            }

            $ta        = $taResp->json('data');
            $mahasiswa = $mhsResp->json('data');

            return view('prodi.tugas_akhir.edit', compact('ta', 'mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->with('error', 'ID Prodi tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_mahasiswa'           => 'required',
            'kategori'               => 'required|string',
            'judul'                  => 'required|string',
            'file_halaman_dpn'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_lembar_pengesahan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_halaman_dpn')) {
                $file = $request->file('file_halaman_dpn');
                $req = $req->attach('file_halaman_dpn', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            if ($request->hasFile('file_lembar_pengesahan')) {
                $file = $request->file('file_lembar_pengesahan');
                $req = $req->attach('file_lembar_pengesahan', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            // method override + fields
            $response = $req->post("{$this->baseUrl}/tugas-akhir/{$id}", [
                '_method'      => 'PUT',
                'id_mahasiswa' => $validated['id_mahasiswa'],
                'kategori'     => $validated['kategori'],
                'judul'        => $validated['judul'],
                'id_prodi'     => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('prodi.tugas_akhir.index')->with('success', 'Tugas akhir berhasil diperbarui');
            }

            return back()->with('error', $response->json('message') ?? 'Gagal memperbarui tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->with('error', 'ID Prodi tidak ditemukan di sesi.');

        try {
            // Use POST + _method override so we can send id_prodi in body
            $response = Http::withToken($token)->asForm()->post("{$this->baseUrl}/tugas-akhir/{$id}", [
                '_method'  => 'DELETE',
                'id_prodi' => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('prodi.tugas_akhir.index')->with('success', 'Tugas akhir berhasil dihapus');
            }

            return back()->with('error', $response->json('message') ?? 'Gagal menghapus tugas akhir');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
