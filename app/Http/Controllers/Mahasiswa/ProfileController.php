<?php

namespace App\Http\Controllers\Mahasiswa;

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
        $id    = Session::get('id'); // id_mahasiswa

        $resp = Http::withToken($token)->get("{$this->baseUrl}/mahasiswa/{$id}");
        if (!$resp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat profil mahasiswa']);
        }

        $mahasiswa = $resp->json('data');
        return view('mahasiswa.profile', compact('mahasiswa'));
    }

    public function update(Request $request)
    {
        $token = Session::get('token');
        $id    = Session::get('id');

        $validated = $request->validate([
            'nama_mahasiswa' => 'required|string|max:255',
            'username'       => 'required|string|max:100',
            'password'       => 'nullable|string|min:6',
            'no_telp'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string|max:255',
            'tanggal_lahir'  => 'nullable|date',
            'tempat_lahir'   => 'nullable|string|max:100',
            'nim_mahasiswa'  => 'required|string|max:50',
        ]);

        if (empty($validated['password'])) unset($validated['password']);

        $resp = Http::withToken($token)->post("{$this->baseUrl}/mahasiswa/{$id}?_method=PUT", $validated);

        return $resp->successful()
            ? back()->with('success', 'Profil mahasiswa berhasil diperbarui')
            : back()->withErrors(['error' => 'Gagal memperbarui profil mahasiswa']);
    }
}
