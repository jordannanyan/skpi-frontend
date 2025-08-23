<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MahasiswaSertifikasiController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    /** Get id_mahasiswa from session->id when role=mahasiswa */
    private function getMahasiswaId(): ?int
    {
        if (Session::get('role') !== 'mahasiswa') return null;
        $id = Session::get('id'); // set at login as id_mahasiswa
        return is_numeric($id) ? (int) $id : null;
    }

    public function index()
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->with('error', 'ID Mahasiswa tidak ditemukan di sesi.');

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/sertifikasi", [
                'id_mahasiswa' => $mhsId, // /sertifikasi?id_mahasiswa=...
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                return view('mahasiswa.sertifikasi.index', compact('data'));
            }
            return back()->with('error', $response->json('message') ?? 'Gagal mengambil data sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->with('error', 'ID Mahasiswa tidak ditemukan di sesi.');

        try {
            // (Opsional) kirim data diri sendiri ke view jika diperlukan
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$mhsId}");
            if ($mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            $mahasiswa = $mhsResp->successful() ? [$mhsResp->json('data')] : [];

            return view('mahasiswa.sertifikasi.create', compact('mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->with('error', 'ID Mahasiswa tidak ditemukan di sesi.');

        // id_mahasiswa diambil dari session (bukan dari form)
        $validated = $request->validate([
            'nama_sertifikasi'      => 'required|string',
            'kategori_sertifikasi'  => 'required|string',
            'file_sertifikat'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/sertifikasi", [
                'id_mahasiswa'         => $mhsId,
                'nama_sertifikasi'     => $validated['nama_sertifikasi'],
                'kategori_sertifikasi' => $validated['kategori_sertifikasi'],
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('mahasiswa.sertifikasi.index')->with('success', 'Sertifikasi berhasil ditambahkan');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal menambahkan sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->with('error', 'ID Mahasiswa tidak ditemukan di sesi.');

        try {
            $sertResp = Http::withToken($token)->get("{$this->baseUrl}/sertifikasi/{$id}", [
                'id_mahasiswa' => $mhsId,
            ]);
            $mhsResp  = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$mhsId}");

            if ($sertResp->status() === 401 || $mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$sertResp->successful()) {
                return back()->with('error', 'Gagal mengambil data sertifikasi');
            }

            $sertifikasi = $sertResp->json('data') ?? [];
            $mahasiswa   = $mhsResp->successful() ? [$mhsResp->json('data')] : [];

            return view('mahasiswa.sertifikasi.edit', compact('sertifikasi', 'mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->with('error', 'ID Mahasiswa tidak ditemukan di sesi.');

        $validated = $request->validate([
            'nama_sertifikasi'      => 'required|string',
            'kategori_sertifikasi'  => 'required|string',
            'file_sertifikat'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/sertifikasi/{$id}", [
                '_method'              => 'PUT',
                'id_mahasiswa'         => $mhsId,
                'nama_sertifikasi'     => $validated['nama_sertifikasi'],
                'kategori_sertifikasi' => $validated['kategori_sertifikasi'],
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('mahasiswa.sertifikasi.index')->with('success', 'Sertifikasi berhasil diperbarui');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal memperbarui sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->with('error', 'ID Mahasiswa tidak ditemukan di sesi.');

        try {
            // Use method override so backend receives id_mahasiswa in body
            $response = Http::withToken($token)->asForm()->post("{$this->baseUrl}/sertifikasi/{$id}", [
                '_method'      => 'DELETE',
                'id_mahasiswa' => $mhsId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('mahasiswa.sertifikasi.index')->with('success', 'Sertifikasi berhasil dihapus');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal menghapus sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
