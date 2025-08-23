<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasSertifikasiController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    /** Ambil id_fakultas dari session->id saat role=fakultas */
    private function getFakultasId(): ?int
    {
        if (Session::get('role') !== 'fakultas') return null;
        $id = Session::get('id'); // diset saat login sebagai id_fakultas
        return is_numeric($id) ? (int) $id : null;
    }

    public function index()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/sertifikasi", [
                'id_fakultas' => $fakultasId, // filter milik fakultas ini
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                return view('fakultas.sertifikasi.index', compact('data'));
            }
            return back()->with('error', $response->json('message') ?? 'Gagal mengambil data sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        try {
            // Ambil daftar mahasiswa di bawah fakultas ini
            $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
                'id_fakultas' => $fakultasId,
            ]);

            if ($mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$mhsResp->successful()) {
                return back()->with('error', 'Gagal mengambil data mahasiswa');
            }

            $mahasiswa = $mhsResp->json('data') ?? [];
            return view('fakultas.sertifikasi.create', compact('mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_mahasiswa'         => 'required',
            'nama_sertifikasi'     => 'required|string',
            'kategori_sertifikasi' => 'required|string',
            'file_sertifikat'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token);

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach(
                    'file_sertifikat',
                    fopen($file->getPathname(), 'r'),
                    $file->getClientOriginalName()
                );
            }

            $response = $req->post("{$this->baseUrl}/sertifikasi", [
                'id_mahasiswa'         => $validated['id_mahasiswa'],
                'nama_sertifikasi'     => $validated['nama_sertifikasi'],
                'kategori_sertifikasi' => $validated['kategori_sertifikasi'],
                'id_fakultas'          => $fakultasId, // inject id_fakultas
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('fakultas.sertifikasi.index')->with('success', 'Sertifikasi berhasil ditambahkan');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal menambahkan sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        try {
            $sertResp = Http::withToken($token)->get("{$this->baseUrl}/sertifikasi/{$id}", [
                'id_fakultas' => $fakultasId,
            ]);
            $mhsResp  = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa", [
                'id_fakultas' => $fakultasId,
            ]);

            if ($sertResp->status() === 401 || $mhsResp->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if (!$sertResp->successful() || !$mhsResp->successful()) {
                return back()->with('error', 'Gagal mengambil data sertifikasi atau mahasiswa');
            }

            $sertifikasi = $sertResp->json('data') ?? [];
            $mahasiswa   = $mhsResp->json('data') ?? [];

            return view('fakultas.sertifikasi.edit', compact('sertifikasi', 'mahasiswa'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        $validated = $request->validate([
            'id_mahasiswa'         => 'required',
            'nama_sertifikasi'     => 'required|string',
            'kategori_sertifikasi' => 'required|string',
            'file_sertifikat'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token);

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach(
                    'file_sertifikat',
                    fopen($file->getPathname(), 'r'),
                    $file->getClientOriginalName()
                );
            }

            $response = $req->post("{$this->baseUrl}/sertifikasi/{$id}", [
                '_method'              => 'PUT',
                'id_mahasiswa'         => $validated['id_mahasiswa'],
                'nama_sertifikasi'     => $validated['nama_sertifikasi'],
                'kategori_sertifikasi' => $validated['kategori_sertifikasi'],
                'id_fakultas'          => $fakultasId, // inject id_fakultas
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('fakultas.sertifikasi.index')->with('success', 'Sertifikasi berhasil diperbarui');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal memperbarui sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->with('error', 'ID Fakultas tidak ditemukan di sesi.');

        try {
            // Method override agar id_fakultas bisa ikut di body
            $response = Http::withToken($token)->asForm()->post("{$this->baseUrl}/sertifikasi/{$id}", [
                '_method'     => 'DELETE',
                'id_fakultas' => $fakultasId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }
            if ($response->successful()) {
                return redirect()->route('fakultas.sertifikasi.index')->with('success', 'Sertifikasi berhasil dihapus');
            }
            return back()->with('error', $response->json('message') ?? 'Gagal menghapus sertifikasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
