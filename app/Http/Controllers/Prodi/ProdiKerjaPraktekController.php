<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ProdiKerjaPraktekController extends Controller
{
    private $baseUrl;

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
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $response = Http::withToken($token)->get("{$this->baseUrl}/kerja-praktek", [
            'id_prodi' => $prodiId, // /kerja-praktek?id_prodi=1
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return view('prodi.kerja_praktek.index', compact('data'));
        }

        return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data kerja praktek']);
    }

    public function create()
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
            'id_prodi' => $prodiId,
        ]);

        if ($mhsResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        if (!$mhsResp->successful()) {
            return back()->withErrors(['error' => 'Gagal mengambil data mahasiswa']);
        }

        $mahasiswa = $mhsResp->json('data') ?? [];
        return view('prodi.kerja_praktek.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'   => 'required',
            'nama_kegiatan'  => 'required|string',
            'file_sertifikat'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/kerja-praktek", [
                'id_mahasiswa'  => $validated['id_mahasiswa'],
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'id_prodi'      => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('prodi.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil ditambahkan');
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan kerja praktek']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
            'id_prodi' => $prodiId,
        ]);
        $response = Http::withToken($token)->get("{$this->baseUrl}/kerja-praktek/{$id}", [
            'id_prodi' => $prodiId,
        ]);

        if ($mhsResp->status() === 401 || $response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful() && $mhsResp->successful()) {
            $kerja_praktek = $response->json('data') ?? [];
            $mahasiswa     = $mhsResp->json('data') ?? [];
            return view('prodi.kerja_praktek.edit', compact('kerja_praktek', 'mahasiswa'));
        }

        return back()->withErrors(['error' => 'Gagal mengambil data kerja praktek atau mahasiswa']);
    }

    public function update(Request $request, $id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'id_mahasiswa'   => 'required',
            'nama_kegiatan'  => 'required|string',
            'file_sertifikat'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/kerja-praktek/{$id}", [
                '_method'       => 'PUT',
                'id_mahasiswa'  => $validated['id_mahasiswa'],
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'id_prodi'      => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('prodi.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil diperbarui');
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui kerja praktek']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token   = Session::get('token');
        $prodiId = $this->getProdiId();
        if (!$prodiId) return back()->withErrors(['error' => 'ID Prodi tidak ditemukan di sesi.']);

        try {
            // Use method override so backend can receive id_prodi in body
            $response = Http::withToken($token)->asForm()->post("{$this->baseUrl}/kerja-praktek/{$id}", [
                '_method'  => 'DELETE',
                'id_prodi' => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('prodi.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil dihapus');
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus kerja praktek']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
