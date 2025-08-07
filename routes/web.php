<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Superadmin\MahasiswaController;
use App\Http\Controllers\Superadmin\FakultasController;
use App\Http\Controllers\Superadmin\ProdiController;
use App\Http\Controllers\Superadmin\CplController;
use App\Http\Controllers\Superadmin\CplSkorController;
use App\Http\Controllers\Superadmin\IsiCapaianController;
use App\Http\Controllers\Superadmin\KategoriController;
use App\Http\Controllers\Superadmin\PengajuanController;
use App\Http\Controllers\Superadmin\PengesahanController;
use App\Http\Controllers\Superadmin\TugasAkhirController;
use App\Http\Controllers\Superadmin\KerjaPraktekController;
use App\Http\Controllers\Superadmin\SertifikasiController;

use App\Http\Controllers\Fakultas\FakultasMahasiswaController;
use App\Http\Controllers\Fakultas\FakultasProdiController;
use App\Http\Controllers\Fakultas\FakultasCplController;
use App\Http\Controllers\Fakultas\FakultasCplSkorController;
use App\Http\Controllers\Fakultas\FakultasIsiCapaianController;
use App\Http\Controllers\Fakultas\FakultasKategoriController;
use App\Http\Controllers\Fakultas\FakultasPengajuanController;
use App\Http\Controllers\Fakultas\FakultasPengesahanController;
use App\Http\Controllers\Fakultas\FakultasTugasAkhirController;
use App\Http\Controllers\Fakultas\FakultasKerjaPraktekController;
use App\Http\Controllers\Fakultas\FakultasSertifikasiController;

use App\Http\Controllers\Prodi\ProdiMahasiswaController;
use App\Http\Controllers\Prodi\ProdiProdiController;
use App\Http\Controllers\Prodi\ProdiCplController;
use App\Http\Controllers\Prodi\ProdiCplSkorController;
use App\Http\Controllers\Prodi\ProdiIsiCapaianController;
use App\Http\Controllers\Prodi\ProdiKategoriController;
use App\Http\Controllers\Prodi\ProdiPengajuanController;
use App\Http\Controllers\Prodi\ProdiPengesahanController;
use App\Http\Controllers\Prodi\ProdiTugasAkhirController;
use App\Http\Controllers\Prodi\ProdiKerjaPraktekController;
use App\Http\Controllers\Prodi\ProdiSertifikasiController;

use App\Http\Controllers\Mahasiswa\MahasiswaPengajuanController;
use App\Http\Controllers\Mahasiswa\MahasiswaPengesahanController;
use App\Http\Controllers\Mahasiswa\MahasiswaTugasAkhirController;
use App\Http\Controllers\Mahasiswa\MahasiswaKerjaPraktekController;
use App\Http\Controllers\Mahasiswa\MahasiswaSertifikasiController;

$apiBaseUrl = 'http://127.0.0.1:8000/api'; // <- deklarasi satu kali saja

Route::get('/', function () {
    if (Session::has('token')) {
        return redirect('/dashboard');
    }

    return view('auth.login');
})->name('login');

Route::post('/', function (Request $request) use ($apiBaseUrl) {
    $role = $request->input('role');

    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
        'role' => 'required|in:superadmin,fakultas,prodi,mahasiswa',
    ]);

    // Endpoint sesuai role
    $url = match ($role) {
        'superadmin' => '/login/superadmin',
        'fakultas'   => '/login/fakultas',
        'prodi'      => '/login/prodi',
        'mahasiswa'  => '/login/mahasiswa',
    };

    $response = Http::post($apiBaseUrl . $url, $request->only('username', 'password'));

    if ($response->successful()) {
        $data = $response->json();

        // Simpan token dan role
        Session::put('token', $data['token']);
        Session::put('role', $data['role']);

        // Simpan nama user sesuai role
        if ($role === 'superadmin') {
            Session::put('name', $data['user']['username']);
            Session::put('id', $data['user']['id_super_admin']);
        } else {
            Session::put('nama_' . $role, $data['user']['nama_' . $role]);
            Session::put('id', $data['user']['id_'. $role]);
        }

        // Redirect ke dashboard masing-masing role
        return redirect("/$role/dashboard");
    }

    return back()->withErrors(['login' => 'Username atau password salah.']);
})->name('custom.login');


Route::get('/superadmin/dashboard', function () {
    return view('superadmin.dashboard');
})->middleware('web');

Route::get('/fakultas/dashboard', function () {
    return view('fakultas.dashboard');
})->middleware('web');

Route::get('/prodi/dashboard', function () {
    return view('prodi.dashboard');
})->middleware('web');

Route::get('/mahasiswa/dashboard', function () {
    return view('mahasiswa.dashboard');
})->middleware('web');

Route::post('/logout', function () {
    Session::flush(); // Remove all session data
    return redirect()->route('login');
})->name('logout');

Route::prefix('superadmin/mahasiswa')->name('superadmin.mahasiswa.')->group(function () {
    Route::get('/', [MahasiswaController::class, 'index'])->name('index');
    Route::get('/create', [MahasiswaController::class, 'create'])->name('create');
    Route::post('/', [MahasiswaController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [MahasiswaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MahasiswaController::class, 'update'])->name('update'); 
    Route::delete('/{id}', [MahasiswaController::class, 'destroy'])->name('destroy');
});


Route::prefix('superadmin/fakultas')->name('superadmin.fakultas.')->group(function () {
    Route::get('/', [FakultasController::class, 'index'])->name('index');
    Route::get('/create', [FakultasController::class, 'create'])->name('create');
    Route::post('/', [FakultasController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasController::class, 'update'])->name('update'); // uses _method=PUT
    Route::delete('/{id}/delete', [FakultasController::class, 'destroy'])->name('destroy'); // uses _method=DELETE
});


Route::prefix('superadmin/prodi')->name('superadmin.prodi.')->group(function () {
    Route::get('/', [ProdiController::class, 'index'])->name('index');
    Route::get('/create', [ProdiController::class, 'create'])->name('create');
    Route::post('/', [ProdiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiController::class, 'destroy'])->name('destroy');
});


Route::prefix('superadmin/cpl')->name('superadmin.cpl.')->group(function () {
    Route::get('/', [CplController::class, 'index'])->name('index');
    Route::get('/create', [CplController::class, 'create'])->name('create');
    Route::post('/', [CplController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [CplController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CplController::class, 'update'])->name('update'); // uses _method=PUT
    Route::delete('/{id}/delete', [CplController::class, 'destroy'])->name('destroy'); // uses _method=DELETE
});

Route::prefix('superadmin/cpl-skor')->name('superadmin.cpl_skor.')->group(function () {
    Route::get('/', [CplSkorController::class, 'index'])->name('index');
    Route::get('/create', [CplSkorController::class, 'create'])->name('create');
    Route::post('/', [CplSkorController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [CplSkorController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CplSkorController::class, 'update'])->name('update');
    Route::delete('/{id}', [CplSkorController::class, 'destroy'])->name('destroy');
});

Route::prefix('superadmin/isi-capaian')->name('superadmin.isi_capaian.')->group(function () {
    Route::get('/', [IsiCapaianController::class, 'index'])->name('index');
    Route::get('/create', [IsiCapaianController::class, 'create'])->name('create');
    Route::post('/', [IsiCapaianController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [IsiCapaianController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IsiCapaianController::class, 'update'])->name('update');
    Route::delete('/{id}', [IsiCapaianController::class, 'destroy'])->name('destroy');
});

Route::prefix('superadmin/kategori')->name('superadmin.kategori.')->group(function () {
    Route::get('/', [KategoriController::class, 'index'])->name('index');
    Route::get('/create', [KategoriController::class, 'create'])->name('create');
    Route::post('/', [KategoriController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('edit');
    Route::put('/{id}', [KategoriController::class, 'update'])->name('update');
    Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
});


Route::prefix('superadmin/pengajuan')->name('superadmin.pengajuan.')->group(function () {
    Route::get('/', [PengajuanController::class, 'index'])->name('index');
    Route::get('/create', [PengajuanController::class, 'create'])->name('create');
    Route::post('/', [PengajuanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [PengajuanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PengajuanController::class, 'update'])->name('update');
    Route::delete('/{id}', [PengajuanController::class, 'destroy'])->name('destroy');
});


Route::prefix('superadmin/pengesahan')->name('superadmin.pengesahan.')->group(function () {
    Route::get('/', [PengesahanController::class, 'index'])->name('index');
    Route::get('/create', [PengesahanController::class, 'create'])->name('create');
    Route::post('/', [PengesahanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [PengesahanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PengesahanController::class, 'update'])->name('update');
    Route::delete('/{id}', [PengesahanController::class, 'destroy'])->name('destroy');
});


Route::prefix('superadmin/tugas-akhir')->name('superadmin.tugas_akhir.')->group(function () {
    Route::get('/', [TugasAkhirController::class, 'index'])->name('index');
    Route::get('/create', [TugasAkhirController::class, 'create'])->name('create');
    Route::post('/', [TugasAkhirController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [TugasAkhirController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TugasAkhirController::class, 'update'])->name('update');
    Route::delete('/{id}', [TugasAkhirController::class, 'destroy'])->name('destroy');
});

Route::prefix('superadmin/kerja-praktek')->name('superadmin.kerja_praktek.')->group(function () {
    Route::get('/', [KerjaPraktekController::class, 'index'])->name('index');
    Route::get('/create', [KerjaPraktekController::class, 'create'])->name('create');
    Route::post('/', [KerjaPraktekController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [KerjaPraktekController::class, 'edit'])->name('edit');
    Route::put('/{id}', [KerjaPraktekController::class, 'update'])->name('update');
    Route::delete('/{id}', [KerjaPraktekController::class, 'destroy'])->name('destroy');
});

Route::prefix('superadmin/sertifikasi')->name('superadmin.sertifikasi.')->group(function () {
    Route::get('/', [SertifikasiController::class, 'index'])->name('index');
    Route::get('/create', [SertifikasiController::class, 'create'])->name('create');
    Route::post('/', [SertifikasiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [SertifikasiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SertifikasiController::class, 'update'])->name('update');
    Route::delete('/{id}', [SertifikasiController::class, 'destroy'])->name('destroy');
});

Route::get('/superadmin/pengesahan/print/{id}', [PengesahanController::class, 'print'])->name('superadmin.pengesahan.print');

// Fakultas 

Route::prefix('fakultas/mahasiswa')->name('fakultas.mahasiswa.')->group(function () {
    Route::get('/', [FakultasMahasiswaController::class, 'index'])->name('index');
    Route::get('/create', [FakultasMahasiswaController::class, 'create'])->name('create');
    Route::post('/', [FakultasMahasiswaController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasMahasiswaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasMahasiswaController::class, 'update'])->name('update'); 
    Route::delete('/{id}', [FakultasMahasiswaController::class, 'destroy'])->name('destroy');
});

Route::prefix('fakultas/prodi')->name('fakultas.prodi.')->group(function () {
    Route::get('/', [FakultasProdiController::class, 'index'])->name('index');
    Route::get('/create', [FakultasProdiController::class, 'create'])->name('create');
    Route::post('/', [FakultasProdiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasProdiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasProdiController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasProdiController::class, 'destroy'])->name('destroy');
});


Route::prefix('fakultas/cpl')->name('fakultas.cpl.')->group(function () {
    Route::get('/', [FakultasCplController::class, 'index'])->name('index');
    Route::get('/create', [FakultasCplController::class, 'create'])->name('create');
    Route::post('/', [FakultasCplController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasCplController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasCplController::class, 'update'])->name('update'); // uses _method=PUT
    Route::delete('/{id}/delete', [FakultasCplController::class, 'destroy'])->name('destroy'); // uses _method=DELETE
});

Route::prefix('fakultas/cpl-skor')->name('fakultas.cpl_skor.')->group(function () {
    Route::get('/', [FakultasCplSkorController::class, 'index'])->name('index');
    Route::get('/create', [FakultasCplSkorController::class, 'create'])->name('create');
    Route::post('/', [FakultasCplSkorController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasCplSkorController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasCplSkorController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasCplSkorController::class, 'destroy'])->name('destroy');
});

Route::prefix('fakultas/isi-capaian')->name('fakultas.isi_capaian.')->group(function () {
    Route::get('/', [FakultasIsiCapaianController::class, 'index'])->name('index');
    Route::get('/create', [FakultasIsiCapaianController::class, 'create'])->name('create');
    Route::post('/', [FakultasIsiCapaianController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasIsiCapaianController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasIsiCapaianController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasIsiCapaianController::class, 'destroy'])->name('destroy');
});

Route::prefix('fakultas/kategori')->name('fakultas.kategori.')->group(function () {
    Route::get('/', [FakultasKategoriController::class, 'index'])->name('index');
    Route::get('/create', [FakultasKategoriController::class, 'create'])->name('create');
    Route::post('/', [FakultasKategoriController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasKategoriController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasKategoriController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasKategoriController::class, 'destroy'])->name('destroy');
});


Route::prefix('fakultas/pengajuan')->name('fakultas.pengajuan.')->group(function () {
    Route::get('/', [FakultasPengajuanController::class, 'index'])->name('index');
    Route::get('/create', [FakultasPengajuanController::class, 'create'])->name('create');
    Route::post('/', [FakultasPengajuanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasPengajuanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasPengajuanController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasPengajuanController::class, 'destroy'])->name('destroy');
});


Route::prefix('fakultas/pengesahan')->name('fakultas.pengesahan.')->group(function () {
    Route::get('/', [FakultasPengesahanController::class, 'index'])->name('index');
    Route::get('/create', [FakultasPengesahanController::class, 'create'])->name('create');
    Route::post('/', [FakultasPengesahanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasPengesahanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasPengesahanController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasPengesahanController::class, 'destroy'])->name('destroy');
});


Route::prefix('fakultas/tugas-akhir')->name('fakultas.tugas_akhir.')->group(function () {
    Route::get('/', [FakultasTugasAkhirController::class, 'index'])->name('index');
    Route::get('/create', [FakultasTugasAkhirController::class, 'create'])->name('create');
    Route::post('/', [FakultasTugasAkhirController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasTugasAkhirController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasTugasAkhirController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasTugasAkhirController::class, 'destroy'])->name('destroy');
});

Route::prefix('fakultas/kerja-praktek')->name('fakultas.kerja_praktek.')->group(function () {
    Route::get('/', [FakultasKerjaPraktekController::class, 'index'])->name('index');
    Route::get('/create', [FakultasKerjaPraktekController::class, 'create'])->name('create');
    Route::post('/', [FakultasKerjaPraktekController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasKerjaPraktekController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasKerjaPraktekController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasKerjaPraktekController::class, 'destroy'])->name('destroy');
});

Route::prefix('fakultas/sertifikasi')->name('fakultas.sertifikasi.')->group(function () {
    Route::get('/', [FakultasSertifikasiController::class, 'index'])->name('index');
    Route::get('/create', [FakultasSertifikasiController::class, 'create'])->name('create');
    Route::post('/', [FakultasSertifikasiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FakultasSertifikasiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FakultasSertifikasiController::class, 'update'])->name('update');
    Route::delete('/{id}', [FakultasSertifikasiController::class, 'destroy'])->name('destroy');
});

Route::get('/fakultas/pengesahan/print/{id}', [FakultasPengesahanController::class, 'print'])->name('fakultas.pengesahan.print');

// Prodi 

Route::prefix('prodi/mahasiswa')->name('prodi.mahasiswa.')->group(function () {
    Route::get('/', [ProdiMahasiswaController::class, 'index'])->name('index');
    Route::get('/create', [ProdiMahasiswaController::class, 'create'])->name('create');
    Route::post('/', [ProdiMahasiswaController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiMahasiswaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiMahasiswaController::class, 'update'])->name('update'); 
    Route::delete('/{id}', [ProdiMahasiswaController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/cpl')->name('prodi.cpl.')->group(function () {
    Route::get('/', [ProdiCplController::class, 'index'])->name('index');
    Route::get('/create', [ProdiCplController::class, 'create'])->name('create');
    Route::post('/', [ProdiCplController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiCplController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiCplController::class, 'update'])->name('update'); // uses _method=PUT
    Route::delete('/{id}/delete', [ProdiCplController::class, 'destroy'])->name('destroy'); // uses _method=DELETE
});

Route::prefix('prodi/cpl-skor')->name('prodi.cpl_skor.')->group(function () {
    Route::get('/', [ProdiCplSkorController::class, 'index'])->name('index');
    Route::get('/create', [ProdiCplSkorController::class, 'create'])->name('create');
    Route::post('/', [ProdiCplSkorController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiCplSkorController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiCplSkorController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiCplSkorController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/isi-capaian')->name('prodi.isi_capaian.')->group(function () {
    Route::get('/', [ProdiIsiCapaianController::class, 'index'])->name('index');
    Route::get('/create', [ProdiIsiCapaianController::class, 'create'])->name('create');
    Route::post('/', [ProdiIsiCapaianController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiIsiCapaianController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiIsiCapaianController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiIsiCapaianController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/kategori')->name('prodi.kategori.')->group(function () {
    Route::get('/', [ProdiKategoriController::class, 'index'])->name('index');
    Route::get('/create', [ProdiKategoriController::class, 'create'])->name('create');
    Route::post('/', [ProdiKategoriController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiKategoriController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiKategoriController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiKategoriController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/pengajuan')->name('prodi.pengajuan.')->group(function () {
    Route::get('/', [ProdiPengajuanController::class, 'index'])->name('index');
    Route::get('/create', [ProdiPengajuanController::class, 'create'])->name('create');
    Route::post('/', [ProdiPengajuanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiPengajuanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiPengajuanController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiPengajuanController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/pengesahan')->name('prodi.pengesahan.')->group(function () {
    Route::get('/', [ProdiPengesahanController::class, 'index'])->name('index');
    Route::get('/create', [ProdiPengesahanController::class, 'create'])->name('create');
    Route::post('/', [ProdiPengesahanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiPengesahanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiPengesahanController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiPengesahanController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/tugas-akhir')->name('prodi.tugas_akhir.')->group(function () {
    Route::get('/', [ProdiTugasAkhirController::class, 'index'])->name('index');
    Route::get('/create', [ProdiTugasAkhirController::class, 'create'])->name('create');
    Route::post('/', [ProdiTugasAkhirController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiTugasAkhirController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiTugasAkhirController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiTugasAkhirController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/kerja-praktek')->name('prodi.kerja_praktek.')->group(function () {
    Route::get('/', [ProdiKerjaPraktekController::class, 'index'])->name('index');
    Route::get('/create', [ProdiKerjaPraktekController::class, 'create'])->name('create');
    Route::post('/', [ProdiKerjaPraktekController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiKerjaPraktekController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiKerjaPraktekController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiKerjaPraktekController::class, 'destroy'])->name('destroy');
});

Route::prefix('prodi/sertifikasi')->name('prodi.sertifikasi.')->group(function () {
    Route::get('/', [ProdiSertifikasiController::class, 'index'])->name('index');
    Route::get('/create', [ProdiSertifikasiController::class, 'create'])->name('create');
    Route::post('/', [ProdiSertifikasiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProdiSertifikasiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiSertifikasiController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProdiSertifikasiController::class, 'destroy'])->name('destroy');
});

Route::get('/prodi/pengesahan/print/{id}', [ProdiPengesahanController::class, 'print'])->name('prodi.pengesahan.print');

// Mahasiswa 

Route::prefix('mahasiswa/pengajuan')->name('mahasiswa.pengajuan.')->group(function () {
    Route::get('/', [MahasiswaPengajuanController::class, 'index'])->name('index');
    Route::get('/create', [MahasiswaPengajuanController::class, 'create'])->name('create');
    Route::post('/', [MahasiswaPengajuanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [MahasiswaPengajuanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MahasiswaPengajuanController::class, 'update'])->name('update');
    Route::delete('/{id}', [MahasiswaPengajuanController::class, 'destroy'])->name('destroy');
});

Route::prefix('mahasiswa/pengesahan')->name('mahasiswa.pengesahan.')->group(function () {
    Route::get('/', [MahasiswaPengesahanController::class, 'index'])->name('index');
    Route::get('/create', [MahasiswaPengesahanController::class, 'create'])->name('create');
    Route::post('/', [MahasiswaPengesahanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [MahasiswaPengesahanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MahasiswaPengesahanController::class, 'update'])->name('update');
    Route::delete('/{id}', [MahasiswaPengesahanController::class, 'destroy'])->name('destroy');
});

Route::prefix('mahasiswa/tugas-akhir')->name('mahasiswa.tugas_akhir.')->group(function () {
    Route::get('/', [MahasiswaTugasAkhirController::class, 'index'])->name('index');
    Route::get('/create', [MahasiswaTugasAkhirController::class, 'create'])->name('create');
    Route::post('/', [MahasiswaTugasAkhirController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [MahasiswaTugasAkhirController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MahasiswaTugasAkhirController::class, 'update'])->name('update');
    Route::delete('/{id}', [MahasiswaTugasAkhirController::class, 'destroy'])->name('destroy');
});

Route::prefix('mahasiswa/kerja-praktek')->name('mahasiswa.kerja_praktek.')->group(function () {
    Route::get('/', [MahasiswaKerjaPraktekController::class, 'index'])->name('index');
    Route::get('/create', [MahasiswaKerjaPraktekController::class, 'create'])->name('create');
    Route::post('/', [MahasiswaKerjaPraktekController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [MahasiswaKerjaPraktekController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MahasiswaKerjaPraktekController::class, 'update'])->name('update');
    Route::delete('/{id}', [MahasiswaKerjaPraktekController::class, 'destroy'])->name('destroy');
});

Route::prefix('mahasiswa/sertifikasi')->name('mahasiswa.sertifikasi.')->group(function () {
    Route::get('/', [MahasiswaSertifikasiController::class, 'index'])->name('index');
    Route::get('/create', [MahasiswaSertifikasiController::class, 'create'])->name('create');
    Route::post('/', [MahasiswaSertifikasiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [MahasiswaSertifikasiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MahasiswaSertifikasiController::class, 'update'])->name('update');
    Route::delete('/{id}', [MahasiswaSertifikasiController::class, 'destroy'])->name('destroy');
});

Route::get('/mahasiswa/pengesahan/print/{id}', [PengesahanController::class, 'print'])->name('mahasiswa.pengesahan.print');
