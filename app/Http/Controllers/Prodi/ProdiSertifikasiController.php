<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProdiSertifikasiController extends Controller
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
            // GET /sertifikasi?id_prodi=1
            $response = Http::withToken($token)->get("{$this->baseUrl}/sertifikasi", [
                'id_prodi' => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                $data = $response->json('data');
                return view('prodi.sertifikasi.index', compact('data'));
            }
            return back()->with('error', $response->json('message') ?? 'Gagal mengambil data sertifikasi');
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
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
                'id_prodi' => $prodiId,
            ]);

            if ($mhsResp->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful()) {
                return back()->with('error', 'Gagal mengambil data mahasiswa');
            }

            $mahasiswa = $mhsResp->json('data') ?? [];
            return view('prodi.sertifikasi.create', compact('mahasiswa'));
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
            'id_mahasiswa'        => 'required',
            'nama_sertifikasi'    => 'required|string',
            'kategori_sertifikasi'=> 'required|string',
            'file_sertifikat'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/sertifikasi", [
                'id_mahasiswa'         => $validated['id_mahasiswa'],
                'nama_sertifikasi'     => $validated['nama_sertifikasi'],
                'kategori_sertifikasi' => $validated['kategori_sertifikasi'],
                'id_prodi'             => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('prodi.sertifikasi.index')->with('success', 'Sertifikasi berhasil ditambahkan');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal menambahkan sertifikasi');
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
            $sertResp = Http::withToken($token)->get("{$this->baseUrl}/sertifikasi/{$id}", [
                'id_prodi' => $prodiId,
            ]);
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
                'id_prodi' => $prodiId,
            ]);

            if ($sertResp->status() === 401 || $mhsResp->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$sertResp->successful() || !$mhsResp->successful()) {
                return back()->with('error', 'Gagal memuat data sertifikasi atau mahasiswa');
            }

            $sertifikasi = $sertResp->json('data') ?? [];
            $mahasiswa   = $mhsResp->json('data') ?? [];
            return view('prodi.sertifikasi.edit', compact('sertifikasi', 'mahasiswa'));
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
            'id_mahasiswa'        => 'required',
            'nama_sertifikasi'    => 'required|string',
            'kategori_sertifikasi'=> 'required|string',
            'file_sertifikat'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/sertifikasi/{$id}", [
                '_method'              => 'PUT',
                'id_mahasiswa'         => $validated['id_mahasiswa'],
                'nama_sertifikasi'     => $validated['nama_sertifikasi'],
                'kategori_sertifikasi' => $validated['kategori_sertifikasi'],
                'id_prodi'             => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('prodi.sertifikasi.index')->with('success', 'Sertifikasi berhasil diperbarui');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal memperbarui sertifikasi');
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
            // Use method override so backend receives id_prodi in body
            $response = Http::withToken($token)->asForm()->post("{$this->baseUrl}/sertifikasi/{$id}", [
                '_method'  => 'DELETE',
                'id_prodi' => $prodiId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('prodi.sertifikasi.index')->with('success', 'Sertifikasi berhasil dihapus');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal menghapus sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
