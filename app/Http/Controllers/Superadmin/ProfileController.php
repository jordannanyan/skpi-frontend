<?php

namespace App\Http\Controllers\Superadmin;

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
        $id    = Session::get('id'); // id_super_admin diset saat login

        $resp = Http::withToken($token)->get("{$this->baseUrl}/super-admin/{$id}");
        if (!$resp->successful()) {
            return back()->withErrors(['error' => 'Gagal memuat profil admin']);
        }

        $admin = $resp->json('data');
        return view('superadmin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $token = Session::get('token');
        $id    = Session::get('id');

        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']); // password opsional
        }

        $resp = Http::withToken($token)
            ->post("{$this->baseUrl}/super-admin/{$id}?_method=PUT", $validated);

        return $resp->successful()
            ? back()->with('success', 'Profil admin berhasil diperbarui')
            : back()->withErrors(['error' => 'Gagal memperbarui profil admin']);
    }
}
