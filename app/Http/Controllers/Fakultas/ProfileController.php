<?php

namespace App\Http\Controllers\Fakultas;

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
        $token   = Session::get('token');
        $id      = Session::get('id'); // id_fakultas yang disimpan saat login

        $resp = Http::withToken($token)->get("{$this->baseUrl}/fakultas/{$id}");
        if (!$resp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat profil fakultas']);
        }

        $fakultas = $resp->json('data');
        return view('fakultas.profile', compact('fakultas'));
    }

    public function update(Request $request)
    {
        $token = Session::get('token');
        $id    = Session::get('id');

        $validated = $request->validate([
            'nama_fakultas' => 'required|string|max:255',
            'username'      => 'required|string|max:255',
            'password'      => 'nullable|string|min:6',
            'nama_dekan'    => 'required|string|max:255',
            'nip'           => 'required|string|max:100',
            'alamat'        => 'required|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']); // optional password
        }

        $resp = Http::withToken($token)->post("{$this->baseUrl}/fakultas/{$id}?_method=PUT", $validated);

        return $resp->successful()
            ? back()->with('success', 'Profil fakultas berhasil diperbarui')
            : back()->withErrors(['error' => 'Gagal memperbarui profil fakultas']);
    }
}
