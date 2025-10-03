<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Super admin'],
            ['id' => 2, 'name' => 'Admin'],
            ['id' => 3, 'name' => 'Karyawan'],
            ['id' => 4, 'name' => 'Peserta'],
            ['id' => 5, 'name' => 'Mentor'],
            ['id' => 6, 'name' => 'Sekolah'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}