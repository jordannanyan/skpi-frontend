<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FakultasProdiController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
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
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $response = Http::withToken($token)->get("{$this->baseUrl}/prodi", [
            'id_fakultas' => $fakultasId, // filter prodi milik fakultas ini
        ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return view('fakultas.prodi.index', compact('data'));
        }

        return back()->withErrors(['error' => $response->json('message') ?? 'Gagal mengambil data prodi']);
    }

    public function create()
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        // Ambil hanya fakultas sendiri (view sebelumnya expect list -> bungkus sebagai array)
        $fakResp = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$fakultasId}");

        if ($fakResp->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        $fakultasList = $fakResp->successful() && $fakResp->json('data')
            ? [ $fakResp->json('data') ]
            : [];

        return view('fakultas.prodi.create', compact('fakultasList'));
    }

    public function store(Request $request)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        // id_fakultas diambil dari session, bukan dari form
        $validated = $request->validate([
            'nama_prodi'        => 'required|string',
            'username'          => 'required|string',
            'password'          => 'required|string|min:6',
            'akreditasi'        => 'required|string',
            'sk_akre'           => 'required|string',
            'jenis_jenjang'     => 'required|string',
            'kompetensi_kerja'  => 'required|string',
            'bahasa'            => 'required|string',
            'penilaian'         => 'required|string',
            'jenis_lanjutan'    => 'required|string',
            'alamat'            => 'required|string',
        ]);

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)->post("{$this->baseUrl}/prodi", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('fakultas.prodi.index')->with('success', 'Data prodi berhasil ditambahkan')
            : redirect()->back()->with('error', $response->json('message') ?? 'Gagal menambahkan data prodi')->withInput();
    }

    public function edit($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        // Ambil prodi dengan otorisasi fakultas & fakultas sendiri sebagai list
        $prodiResponse = Http::withToken($token)->get("{$this->baseUrl}/prodi/{$id}", [
            'id_fakultas' => $fakultasId,
        ]);
        $fakultasResponse = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$fakultasId}");

        if ($prodiResponse->status() === 401 || $fakultasResponse->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        if ($prodiResponse->successful()) {
            $prodi = $prodiResponse->json('data') ?? [];
            $fakultasList = $fakultasResponse->successful() && $fakultasResponse->json('data')
                ? [ $fakultasResponse->json('data') ]
                : [];

            return view('fakultas.prodi.edit', compact('prodi', 'fakultasList'));
        }

        return redirect()->back()->with('error', $prodiResponse->json('message') ?? 'Gagal mengambil data prodi');
    }

    public function update(Request $request, $id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withInput()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $validated = $request->validate([
            'nama_prodi'        => 'required|string',
            'username'          => 'required|string',
            'password'          => 'nullable|string|min:6',
            'akreditasi'        => 'required|string',
            'sk_akre'           => 'required|string',
            'jenis_jenjang'     => 'required|string',
            'kompetensi_kerja'  => 'required|string',
            'bahasa'            => 'required|string',
            'penilaian'         => 'required|string',
            'jenis_lanjutan'    => 'required|string',
            'alamat'            => 'required|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $payload = $validated + ['id_fakultas' => $fakultasId];

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/prodi/{$id}?_method=PUT", $payload);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('fakultas.prodi.index')->with('success', 'Data prodi berhasil diperbarui')
            : redirect()->back()->with('error', $response->json('message') ?? 'Gagal memperbarui data prodi')->withInput();
    }

    public function destroy($id)
    {
        $token      = Session::get('token');
        $fakultasId = $this->getFakultasId();
        if (!$fakultasId) return back()->withErrors(['error' => 'ID Fakultas tidak ditemukan di sesi.']);

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$this->baseUrl}/prodi/{$id}?_method=DELETE", [
                'id_fakultas' => $fakultasId,
            ]);

        if ($response->status() === 401) {
            return redirect()->route('login')->withErrors(['login' => 'Sesi berakhir, silakan login ulang.']);
        }

        return $response->successful()
            ? redirect()->route('fakultas.prodi.index')->with('success', 'Data prodi berhasil dihapus')
            : redirect()->back()->with('error', $response->json('message') ?? 'Gagal menghapus data prodi');
    }
}
