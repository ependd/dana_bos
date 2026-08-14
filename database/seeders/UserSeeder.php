<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed sample users: 1 Admin, 1 Kepala Sekolah, 2 Guru.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Bendahara',
            'email' => 'admin@smpn40.sch.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'nip' => '198501012010011001',
        ]);

        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepsek@smpn40.sch.id',
            'password' => bcrypt('password'),
            'role' => 'kepala_sekolah',
            'nip' => '197001011995031001',
        ]);

        User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru1@smpn40.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'nip' => '199001012015012001',
        ]);

        User::create([
            'name' => 'Guru IPA',
            'email' => 'guru2@smpn40.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'nip' => '199201012016011001',
        ]);
    }
}
