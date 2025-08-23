<?php

// namespace App\Http\Controllers\Mahasiswa;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Session;

// class MahasiswaPengajuanController extends Controller
// {
//     protected $baseUrl;

//     public function __construct()
//     {
//         $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
//     }

//     public function index()
//     {
//         $response = Http::get($this->baseUrl);
//         $data = $response->json('data');
//         return view('mahasiswa.pengajuan.index', compact('data'));
//     }

//     public function create()
//     {
//         $mahasiswa = Http::get('http://127.0.0.1:8000/api/mahasiswa')->json('data');
//         $kategori = Http::get('http://127.0.0.1:8000/api/kategori')->json('data');

//         return view('mahasiswa.pengajuan.create', compact('mahasiswa', 'kategori'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'id_mahasiswa' => 'required|numeric',
//             'id_kategori' => 'required|numeric',
//             'status' => 'required|in:aktif,noaktif',
//             'tgl_pengajuan' => 'required|date'
//         ]);

//         $response = Http::post($this->baseUrl, $validated);

//         return redirect()->route('mahasiswa.pengajuan.index')->with('success', 'Data pengajuan berhasil ditambahkan');
//     }

//     public function edit($id)
//     {
//         $pengajuan = Http::get("{$this->baseUrl}/{$id}")->json('data');
//         $mahasiswa = Http::get('http://127.0.0.1:8000/api/mahasiswa')->json('data');
//         $kategori = Http::get('http://127.0.0.1:8000/api/kategori')->json('data');

//         return view('mahasiswa.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
//     }

//     public function update(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'id_mahasiswa' => 'required|numeric',
//             'id_kategori' => 'required|numeric',
//             'status' => 'required|in:aktif,noaktif',
//             'tgl_pengajuan' => 'required|date'
//         ]);

//         $response = Http::asForm()->post("{$this->baseUrl}/{$id}?_method=PUT", $validated);

//         return redirect()->route('mahasiswa.pengajuan.index')->with('success', 'Data pengajuan berhasil diperbarui');
//     }

//     public function destroy($id)
//     {
//         $response = Http::asForm()->post("{$this->baseUrl}/{$id}?_method=DELETE");

//         return redirect()->route('mahasiswa.pengajuan.index')->with('success', 'Data pengajuan berhasil dihapus');
//     }
//}


namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MahasiswaPengajuanController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        // tetap gunakan base URL yang sudah ada agar tidak ganggu file lain
        $this->baseUrl = 'http://127.0.0.1:8000/api/pengajuan';
    }

    public function index()
    {
        try {
            $response = Http::get($this->baseUrl);
            $data = $response->json('data');
        } catch (\Throwable $e) {
            $data = [];
            Session::flash('error', 'Gagal mengambil data pengajuan');
        }

        return view('mahasiswa.pengajuan.index', compact('data'));
    }

    public function create()
    {
        // cukup ambil mahasiswa untuk fallback nama di Blade
        $mahasiswa = Http::get('http://127.0.0.1:8000/api/mahasiswa')->json('data');

        // kategori tidak perlu dikirim ke view lagi
        return view('mahasiswa.pengajuan.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        // mahasiswa saja yang divalidasi dari form
        $validated = $request->validate([
            'id_mahasiswa' => 'required|numeric',
        ]);

        // Tentukan id_kategori default secara otomatis
        $kategoriId = null;
        try {
            $respKat = Http::get('http://127.0.0.1:8000/api/kategori');
            if ($respKat->successful()) {
                $listKat = collect($respKat->json('data') ?? []);
                // cari kategori bernama 'SKPI', jika tidak ada ambil yang pertama
                $defaultKat = $listKat->firstWhere('nama_kategori', 'SKPI') ?? $listKat->first();
                $kategoriId = data_get($defaultKat, 'id_kategori');
            }
        } catch (\Throwable $e) {
            // biarkan $kategoriId tetap null, nanti fallback ke 1
        }

        if (!$kategoriId) {
            // fallback aman: 1 (silakan sesuaikan jika ID kategori default berbeda)
            $kategoriId = 1;
        }

        // Siapkan payload otomatis
        $payload = [
            'id_mahasiswa'  => (int) $validated['id_mahasiswa'],
            'id_kategori'   => $kategoriId,
            'status'        => 'aktif', // sesuai validasi API kamu: in:aktif,noaktif
            'tgl_pengajuan' => now()->toDateString(), // otomatis tanggal hari ini (server)
        ];

        // Kirim ke API
        Http::post($this->baseUrl, $payload);

        return redirect()
            ->route('mahasiswa.pengajuan.index')
            ->with('success', 'Data pengajuan berhasil ditambahkan');
    }

    public function edit($id)
    {
        // biarkan (masih sesuai alur lama yang ada tombol Edit)
        $pengajuan = Http::get("{$this->baseUrl}/{$id}")->json('data');
        $mahasiswa = Http::get('http://127.0.0.1:8000/api/mahasiswa')->json('data');
        $kategori  = Http::get('http://127.0.0.1:8000/api/kategori')->json('data');

        return view('mahasiswa.pengajuan.edit', compact('pengajuan', 'mahasiswa', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        // biarkan dulu alur lama agar tidak ganggu fitur lain
        $validated = $request->validate([
            'id_mahasiswa'  => 'required|numeric',
            'id_kategori'   => 'required|numeric',
            'status'        => 'required|in:aktif,noaktif',
            'tgl_pengajuan' => 'required|date'
        ]);

        Http::asForm()->post("{$this->baseUrl}/{$id}?_method=PUT", $validated);

        return redirect()
            ->route('mahasiswa.pengajuan.index')
            ->with('success', 'Data pengajuan berhasil diperbarui');
    }

    public function destroy($id)
    {
        Http::asForm()->post("{$this->baseUrl}/{$id}?_method=DELETE");

        return redirect()
            ->route('mahasiswa.pengajuan.index')
            ->with('success', 'Data pengajuan berhasil dihapus');
    }
}
