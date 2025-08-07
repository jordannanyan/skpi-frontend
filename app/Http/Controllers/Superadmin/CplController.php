<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CplController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->baseUrl}/cpl");

        if ($response->successful()) {
            $data = $response->json('data');
            return view('superadmin.cpl.index', compact('data'));
        }

        return back()->withErrors(['error' => 'Gagal mengambil data CPL']);
    }

    public function create()
    {
        return view('superadmin.cpl.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_cpl' => 'required|string',
            'status' => 'required|in:aktif,noaktif'
        ]);

        $response = Http::post("{$this->baseUrl}/cpl", $validated);

        if ($response->successful()) {
            return redirect()->route('superadmin.cpl.index')->with('success', 'CPL berhasil ditambahkan');
        }

        return back()->withErrors(['error' => 'Gagal menambahkan CPL']);
    }

    public function edit($id)
    {
        $response = Http::get("{$this->baseUrl}/cpl/{$id}");

        if ($response->successful()) {
            $cpl = $response->json('data');
            return view('superadmin.cpl.edit', compact('cpl'));
        }

        return redirect()->back()->with('error', 'Gagal mengambil data CPL');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_cpl' => 'required|string',
            'status' => 'required|in:aktif,noaktif'
        ]);

        $response = Http::asForm()->post("{$this->baseUrl}/cpl/{$id}?_method=PUT", $validated);

        if ($response->successful()) {
            return redirect()->route('superadmin.cpl.index')->with('success', 'CPL berhasil diperbarui');
        }

        return back()->withErrors(['error' => 'Gagal memperbarui CPL']);
    }

    public function destroy($id)
    {
        $response = Http::asForm()->post("{$this->baseUrl}/cpl/{$id}?_method=DELETE");

        if ($response->successful()) {
            return redirect()->route('superadmin.cpl.index')->with('success', 'CPL berhasil dihapus');
        }

        return redirect()->back()->with('error', 'Gagal menghapus CPL');
    }
}