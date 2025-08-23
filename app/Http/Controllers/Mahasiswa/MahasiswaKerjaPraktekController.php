<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class MahasiswaKerjaPraktekController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    /** Get id_mahasiswa from session->id when role=mahasiswa */
    private function getMahasiswaId(): ?int
    {
        if (Session::get('role') !== 'mahasiswa') return null;
        $id = Session::get('id'); // set at login for role=mahasiswa as id_mahasiswa
        return is_numeric($id) ? (int) $id : null;
    }

    public function index()
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->withErrors(['error' => 'ID Mahasiswa tidak ditemukan di sesi.']);

        $response = Http::withToken($token)->get("{$this->baseUrl}/kerja-praktek", [
            'id_mahasiswa' => $mhsId, // => /kerja-praktek?id_mahasiswa=XYZ
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return view('mahasiswa.kerja_praktek.index', compact('data'));
        }

        return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data kerja praktek']);
    }

    public function create()
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->withErrors(['error' => 'ID Mahasiswa tidak ditemukan di sesi.']);

        // (Opsional) jika view butuh data mahasiswa, ambil profil diri sendiri
        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$mhsId}");
        if ($mhsResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }
        $mahasiswa = $mhsResp->successful() ? [$mhsResp->json('data')] : [];

        return view('mahasiswa.kerja_praktek.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->withErrors(['error' => 'ID Mahasiswa tidak ditemukan di sesi.']);

        // id_mahasiswa diambil dari session (bukan dari form)
        $validated = $request->validate([
            'nama_kegiatan'   => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/kerja-praktek", [
                'id_mahasiswa'  => $mhsId,
                'nama_kegiatan' => $validated['nama_kegiatan'],
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('mahasiswa.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil ditambahkan');
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menambahkan kerja praktek']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->withErrors(['error' => 'ID Mahasiswa tidak ditemukan di sesi.']);

        // (Opsional) kirim data mahasiswa sendiri ke view jika diperlukan
        $mhsResp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$mhsId}");
        $response = Http::withToken($token)->get("{$this->baseUrl}/kerja-praktek/{$id}", [
            'id_mahasiswa' => $mhsId,
        ]);

        if ($mhsResp->status() === 401 || $response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $kerja_praktek = $response->json('data') ?? [];
            $mahasiswa     = $mhsResp->successful() ? [$mhsResp->json('data')] : [];
            return view('mahasiswa.kerja_praktek.edit', compact('kerja_praktek', 'mahasiswa'));
        }

        return back()->withErrors(['error' => 'Gagal mengambil data kerja praktek']);
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->withErrors(['error' => 'ID Mahasiswa tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'nama_kegiatan'   => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $req = Http::withToken($token)->asMultipart();

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $req  = $req->attach('file_sertifikat', fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/kerja-praktek/{$id}", [
                '_method'       => 'PUT',
                'id_mahasiswa'  => $mhsId,
                'nama_kegiatan' => $validated['nama_kegiatan'],
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('mahasiswa.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil diperbarui');
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal memperbarui kerja praktek']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('token');
        $mhsId = $this->getMahasiswaId();
        if (!$mhsId) return back()->withErrors(['error' => 'ID Mahasiswa tidak ditemukan di sesi.']);

        try {
            // Override method so backend can receive id_mahasiswa in body
            $response = Http::withToken($token)->asForm()->post("{$this->baseUrl}/kerja-praktek/{$id}", [
                '_method'      => 'DELETE',
                'id_mahasiswa' => $mhsId,
            ]);

            if ($response->status() === 401) {
                return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
            }

            if ($response->successful()) {
                return redirect()->route('mahasiswa.kerja_praktek.index')->with('success', 'Data kerja praktek berhasil dihapus');
            }

            return back()->withErrors(['error' => $response->json('message') ?? 'Gagal menghapus kerja praktek']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
