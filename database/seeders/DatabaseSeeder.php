<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Role
        $roles = [
            ['nama_role' => 'admin', 'deskripsi' => 'Administrator Sistem'],
            ['nama_role' => 'guru', 'deskripsi' => 'Guru Mata Pelajaran / Wali Kelas'],
            ['nama_role' => 'bk', 'deskripsi' => 'Guru Bimbingan Konseling'],
            ['nama_role' => 'waka_kesiswaan', 'deskripsi' => 'Wakil Kepala Sekolah Bidang Kesiswaan'],
            ['nama_role' => 'kepala_sekolah', 'deskripsi' => 'Kepala Sekolah'],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }

        // 2. Buat Data Jurusan[cite: 8]
        $jurusans = [
            ['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak'],
            ['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer dan Jaringan'],
            ['kode_jurusan' => 'TKR', 'nama_jurusan' => 'Teknik Kendaraan Ringan'],
            ['kode_jurusan' => 'TSM', 'nama_jurusan' => 'Teknik Sepeda Motor'],
            ['kode_jurusan' => 'BUS', 'nama_jurusan' => 'Tata Busana'],
        ];
        foreach ($jurusans as $jurusan) {
            Jurusan::create($jurusan);
        }

        // 3. Buat Akun Pengguna (Dummy) untuk Testing Login[cite: 8]
        $password = Hash::make('password123'); // Password default untuk semua akun

        User::insert([
            [
                'role_id' => 1, // Admin
                'nip_nik' => '111111',
                'nama' => 'Admin Sekolah',
                'email' => 'admin@smksa.com',
                'password' => $password,
                'jurusan_id' => null,
                'is_koordinator_bk' => false,
                'status_aktif' => true,
            ],
            [
                'role_id' => 2, // Guru
                'nip_nik' => '222222',
                'nama' => 'Bapak Bachtiar, S.Kom',
                'email' => 'guru@smksa.com',
                'password' => $password,
                'jurusan_id' => 1, // RPL
                'is_koordinator_bk' => false,
                'status_aktif' => true,
            ],
            [
                'role_id' => 3, // BK
                'nip_nik' => '333333',
                'nama' => 'Ibu Siti, S.Pd',
                'email' => 'bk@smksa.com',
                'password' => $password,
                'jurusan_id' => null,
                'is_koordinator_bk' => true, // Menjabat sebagai Koordinator BK[cite: 8]
                'status_aktif' => true,
            ],
            [
                'role_id' => 4, // Waka Kesiswaan
                'nip_nik' => '444444',
                'nama' => 'Drs. Sudirman',
                'email' => 'waka@smksa.com',
                'password' => $password,
                'jurusan_id' => null,
                'is_koordinator_bk' => false,
                'status_aktif' => true,
            ],
            [
                'role_id' => 5, // Kepala Sekolah
                'nip_nik' => '555555',
                'nama' => 'Bapak Kepala Sekolah, M.Pd',
                'email' => 'kepsek@smksa.com',
                'password' => $password,
                'jurusan_id' => null,
                'is_koordinator_bk' => false,
                'status_aktif' => true,
            ],
        ]);

    $jams = [
            ['jam_ke' => 1,  'waktu_mulai' => '07:00:00', 'waktu_selesai' => '07:45:00'],
            ['jam_ke' => 2,  'waktu_mulai' => '07:45:00', 'waktu_selesai' => '08:30:00'],
            ['jam_ke' => 3,  'waktu_mulai' => '08:30:00', 'waktu_selesai' => '09:15:00'],
            ['jam_ke' => 4,  'waktu_mulai' => '09:15:00', 'waktu_selesai' => '10:00:00'],
            ['jam_ke' => 5,  'waktu_mulai' => '10:15:00', 'waktu_selesai' => '11:00:00'],
            ['jam_ke' => 6,  'waktu_mulai' => '11:00:00', 'waktu_selesai' => '11:45:00'],
            ['jam_ke' => 7,  'waktu_mulai' => '13:00:00', 'waktu_selesai' => '13:45:00'],
            ['jam_ke' => 8,  'waktu_mulai' => '13:45:00', 'waktu_selesai' => '14:30:00'],
            ['jam_ke' => 9,  'waktu_mulai' => '14:30:00', 'waktu_selesai' => '15:15:00'],
            ['jam_ke' => 10, 'waktu_mulai' => '15:15:00', 'waktu_selesai' => '16:00:00'],
        ];
        foreach ($jams as $jam) {
            DB::table('jam_pelajarans')->updateOrInsert(
                ['jam_ke' => $jam['jam_ke']],   // kunci unik
                [
                    'waktu_mulai'   => $jam['waktu_mulai'],
                    'waktu_selesai' => $jam['waktu_selesai'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }
        $this->command->info('✅ Jam Pelajaran di-seed: ' . count($jams));
    }
}   