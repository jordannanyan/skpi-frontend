<?php

namespace App\Helpers;

class AdminMenu
{
    public static function getMenu()
    {
        $role = session('role');

        if ($role === 'superadmin') {
            return [
                ['header' => 'Dashboard'],
                [
                    'text' => 'Dashboard',
                    'url'  => '/',
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
                // ... (rest of superadmin menu)
            ];
        }

        return [
            ['header' => 'Dashboard'],
            [
                'text' => 'Dashboard',
                'url'  => '/',
                'icon' => 'fas fa-home',
            ],
        ];
    }
}
