<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $role = session('role');

            if ($role === 'superadmin') {
                $event->menu->add(
                    ['header' => 'Dashboard'],
                    [
                        'text' => 'Dashboard',
                        'url'  => 'superadmin/dashboard',
                        'icon' => 'fas fa-tachometer-alt',
                    ],

                    ['header' => 'Manajemen Akun'],
                    [
                        'text' => 'Kelola Mahasiswa',
                        'url'  => 'superadmin/mahasiswa',
                        'icon' => 'fas fa-user-graduate',
                    ],
                    [
                        'text' => 'Kelola Fakultas',
                        'url'  => 'superadmin/fakultas',
                        'icon' => 'fas fa-university',
                    ],
                    [
                        'text' => 'Kelola Prodi',
                        'url'  => 'superadmin/prodi',
                        'icon' => 'fas fa-building',
                    ],
                    ['header' => 'Aktivitas Akademik'],
                    [
                        'text' => 'Kelola Tugas Akhir',
                        'url'  => 'superadmin/tugas-akhir',
                        'icon' => 'fas fa-book',
                    ],
                    [
                        'text' => 'Kelola Kerja Praktek',
                        'url'  => 'superadmin/kerja-praktek',
                        'icon' => 'fas fa-briefcase',
                    ],
                    [
                        'text' => 'Kelola Sertifikasi',
                        'url'  => 'superadmin/sertifikasi',
                        'icon' => 'fas fa-certificate',
                    ],
                    ['header' => 'Manajemen Akademik'],
                    [
                        'text' => 'Kelola Kategori CPL',
                        'url'  => 'superadmin/cpl',
                        'icon' => 'fas fa-list',
                    ],
                    [
                        'text' => 'Kelola CPL Master',
                        'url'  => 'superadmin/cpl-master',
                        'icon' => 'fas fa-list',
                    ],
                    [
                        'text' => 'Kelola CPL',
                        'url'  => 'superadmin/cpl-nilai',
                        'icon' => 'fas fa-star-half-alt',
                    ],

                    ['header' => 'Manajemen SKPI'],
                    [
                        'text' => 'Kelola Pengajuan SKPI',
                        'url'  => 'superadmin/pengajuan',
                        'icon' => 'fas fa-paper-plane',
                    ],
                    [
                        'text' => 'Kelola Pengesahan',
                        'url'  => 'superadmin/pengesahan',
                        'icon' => 'fas fa-stamp',
                    ],

                );
            } else 
            if ($role === 'fakultas') {
                $event->menu->add(
                    ['header' => 'Dashboard'],
                    [
                        'text' => 'Dashboard',
                        'url'  => '/fakultas/dashboard',
                        'icon' => 'fas fa-tachometer-alt',
                    ],
                    ['header' => 'Manajemen SKPI'],
                    [
                        'text' => 'Daftar Pengajuan SKPI',
                        'url'  => 'fakultas/pengajuan',
                        'icon' => 'fas fa-paper-plane',
                    ],
                    [
                        'text' => 'Kelola Pengesahan',
                        'url'  => 'fakultas/pengesahan',
                        'icon' => 'fas fa-stamp',
                    ],
                );
            } else 
            if ($role === 'prodi') {
                $event->menu->add(
                    ['header' => 'Dashboard'],
                    [
                        'text' => 'Dashboard',
                        'url'  => '/prodi/dashboard',
                        'icon' => 'fas fa-tachometer-alt',
                    ],

                    ['header' => 'Manajemen Akun'],
                    [
                        'text' => 'Kelola Mahasiswa',
                        'url'  => 'prodi/mahasiswa',
                        'icon' => 'fas fa-user-graduate',
                    ],
                    ['header' => 'Aktivitas Akademik'],
                    [
                        'text' => 'Kelola Tugas Akhir',
                        'url'  => 'prodi/tugas-akhir',
                        'icon' => 'fas fa-book',
                    ],
                    [
                        'text' => 'Kelola Kerja Praktek',
                        'url'  => 'prodi/kerja-praktek',
                        'icon' => 'fas fa-briefcase',
                    ],
                    [
                        'text' => 'Kelola Sertifikasi',
                        'url'  => 'prodi/sertifikasi',
                        'icon' => 'fas fa-certificate',
                    ],
                    ['header' => 'Manajemen Akademik'],
                    [
                        'text' => 'Kelola CPL',
                        'url'  => 'prodi/cpl-nilai',
                        'icon' => 'fas fa-star-half-alt',
                    ],

                    ['header' => 'Manajemen SKPI'],
                    [
                        'text' => 'Kelola Pengajuan SKPI',
                        'url'  => 'prodi/pengajuan',
                        'icon' => 'fas fa-paper-plane',
                    ],
                );
            } else 
            if ($role === 'mahasiswa') {
                $event->menu->add(
                    ['header' => 'Dashboard'],
                    [
                        'text' => 'Dashboard',
                        'url'  => '/mahasiswa/dashboard',
                        'icon' => 'fas fa-tachometer-alt',
                    ],

                    ['header' => 'Aktivitas Akademik'],
                    [
                        'text' => 'Kelola Tugas Akhir',
                        'url'  => 'mahasiswa/tugas-akhir',
                        'icon' => 'fas fa-book',
                    ],
                    [
                        'text' => 'Kelola Kerja Praktek',
                        'url'  => 'mahasiswa/kerja-praktek',
                        'icon' => 'fas fa-briefcase',
                    ],
                    [
                        'text' => 'Kelola Sertifikasi',
                        'url'  => 'mahasiswa/sertifikasi',
                        'icon' => 'fas fa-certificate',
                    ],
                );
            } else {
                $event->menu->add(
                    ['header' => 'Dashboard'],
                    [
                        'text' => 'Dashboard',
                        'url'  => '/',
                        'icon' => 'fas fa-tachometer-alt',
                    ]
                );
            }
        });
    }
}
