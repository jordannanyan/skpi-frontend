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

        // 1) Validasi dasar. Password baru opsional, tetapi jika diisi harus dikonfirmasi
        $validated = $request->validate([
            'username'             => 'required|string|max:100',
            'password'             => 'nullable|string|min:8|confirmed',
            'current_password'     => 'required_with:password|string',
        ], [
            'password.min'                 => 'Password baru minimal 8 karakter.',
            'password.confirmed'           => 'Konfirmasi password baru tidak cocok.',
            'current_password.required_with' => 'Password saat ini wajib diisi untuk mengganti password.',
        ]);

        // 2) Siapkan payload update
        $payload = [
            'username' => $validated['username'],
        ];
        if ($request->filled('password')) {
            $payload['password'] = $validated['password'];
            // Kirim juga konfirmasi jika API kamu memvalidasi di sisi server
            if ($request->has('password_confirmation')) {
                $payload['password_confirmation'] = $request->input('password_confirmation');
            }
        }

        // 3) Jika mau ganti password, verifikasi current password ke API terlebih dahulu
        if ($request->filled('password')) {
            // Ambil username lama agar verifikasi tidak terpengaruh bila user ganti username sekaligus
            $profileResp = Http::withToken($token)->get("{$this->baseUrl}/super-admin/{$id}");
            if (!$profileResp->successful()) {
                return back()->withErrors(['error' => 'Gagal mengambil profil untuk verifikasi.'])->withInput();
            }
            $currentUsername = data_get($profileResp->json(), 'data.username');

            // Lakukan verifikasi ke endpoint login API
            // Ubah endpoint atau field sesuai API kamu bila berbeda
            $loginResp = Http::post("{$this->baseUrl}/auth/login", [
                'username' => $currentUsername,
                'password' => $request->input('current_password'),
            ]);

            if (!$loginResp->successful()) {
                // Bisa 401, 422, dsb. Kita standar-kan pesannya
                return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
            }
        }

        // 4) Lanjut update profil
        $resp = Http::withToken($token)
            ->post("{$this->baseUrl}/super-admin/{$id}?_method=PUT", $payload);

        if ($resp->successful()) {
            return back()->with('success', 'Profil admin berhasil diperbarui');
        }

        // Tampilkan detail error dari API jika ada
        $msg = $resp->json('message') ?? 'Gagal memperbarui profil admin';
        return back()->withErrors(['error' => $msg])->withInput();
    }
}
