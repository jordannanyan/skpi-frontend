<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    public function show()
    {
        $token = Session::get('token');
        $id    = Session::get('id'); // id_prodi

        $resp = Http::withToken($token)->get("{$this->baseUrl}/prodi/{$id}");
        if (!$resp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat profil prodi']);
        }

        $prodi = $resp->json('data');
        return view('prodi.profile', compact('prodi'));
    }

    public function update(Request $request)
    {
        $token = Session::get('token');
        $id    = Session::get('id');

        $validated = $request->validate([
            'nama_prodi'       => 'required|string|max:255',
            'username'         => 'required|string|max:255',
            'password'         => 'nullable|string|min:6',
            'akreditasi'       => 'nullable|string|max:10',
            'sk_akre'          => 'nullable|string|max:100',
            'jenis_jenjang'    => 'nullable|string|max:50',
            'kompetensi_kerja' => 'nullable|string|max:255',
            'bahasa'           => 'nullable|string|max:100',
            'penilaian'        => 'nullable|string|max:100',
            'jenis_lanjutan'   => 'nullable|string|max:100',
            'alamat'           => 'nullable|string',
        ]);

        if (empty($validated['password'])) unset($validated['password']);

        $resp = Http::withToken($token)->post("{$this->baseUrl}/prodi/{$id}?_method=PUT", $validated);

        return $resp->successful()
            ? back()->with('success', 'Profil prodi berhasil diperbarui')
            : back()->withErrors(['error' => 'Gagal memperbarui profil prodi']);
    }
}
