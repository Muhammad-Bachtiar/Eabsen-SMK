<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\SimpleExcel\SimpleExcelReader;

class GuruController extends Controller
{
    public function index()
    {
        // Menampilkan seluruh staf sekolah (Guru, BK, Waka, Kepsek)
        $roleGuruIds = Role::whereIn('nama_role', ['guru', 'bk', 'waka_kesiswaan', 'kepala_sekolah'])->pluck('id');
        $gurus = User::with('role')->whereIn('role_id', $roleGuruIds)->latest()->get();

        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        // Filter role khusus untuk staf (tanpa admin)
        $roles = Role::whereIn('nama_role', ['guru', 'bk', 'waka_kesiswaan', 'kepala_sekolah'])
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.guru.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id',
            'nip_nik'  => 'nullable|string|max:50',
        ]);

        User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'nip_nik'  => $request->nip_nik,
        ]);

        return redirect()->route('admin.guru.index')->with('success', 'Akun pengajar/staf berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $guru = User::findOrFail($id);
        
        $roles = Role::whereIn('nama_role', ['guru', 'bk', 'waka_kesiswaan', 'kepala_sekolah'])
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.guru.edit', compact('guru', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $guru = User::findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'role_id'  => 'required|exists:roles,id',
            'nip_nik'  => 'nullable|string|max:50',
        ]);

        $data = [
            'nama'    => $request->nama,
            'email'   => $request->email,
            'role_id' => $request->role_id,
            'nip_nik' => $request->nip_nik,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data pengajar/staf berhasil diperbarui!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Akun pengajar/staf berhasil dihapus!');
    }

    /**
     * Import Data Guru/Staf dari File Excel
     */
/**
     * Import Data Guru/Staf via Excel dengan Notifikasi Duplikasi Lengkap
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->getPathname();
            $extension = $file->getClientOriginalExtension();

            $rows = SimpleExcelReader::create($filePath, $extension)->getRows();

            $defaultRole = Role::where('nama_role', 'guru')->first();
            $defaultRoleId = $defaultRole ? $defaultRole->id : 2;

            $rolesMap = Role::all()->pluck('id', 'nama_role')->mapWithKeys(function ($id, $name) {
                return [strtolower(trim($name)) => $id];
            })->toArray();

            $insertedCount = 0;
            $warnings = [];

            foreach ($rows as $index => $row) {
                $barisExcel = $index + 2; // Menyesuaikan baris Excel (baris 1 = header)

                $nama    = trim($row['nama'] ?? '');
                $email   = trim($row['email'] ?? '');
                $nipNik  = trim($row['nip_nik'] ?? '');
                $roleRaw = strtolower(trim($row['role'] ?? 'guru'));

                // Skip jika kolom utama kosong
                if (empty($nama) || empty($email)) {
                    continue;
                }

                // 1. Cek Duplikasi Email di Database
                if (User::where('email', $email)->exists()) {
                    $warnings[] = "Baris {$barisExcel}: Email '{$email}' sudah pernah diinputkan.";
                    continue;
                }

                // 2. Cek Duplikasi NIP/NIK di Database (jika NIP/NIK diisi)
                if (!empty($nipNik) && User::where('nip_nik', $nipNik)->exists()) {
                    $warnings[] = "Baris {$barisExcel}: NIP/NIK '{$nipNik}' ({$nama}) sudah pernah diinputkan.";
                    continue;
                }

                // 3. Cek Duplikasi Nama di Database
                if (User::where('nama', $nama)->exists()) {
                    $warnings[] = "Baris {$barisExcel}: Nama '{$nama}' sudah pernah diinputkan.";
                    continue;
                }

                // Normalisasi Role
                if (in_array($roleRaw, ['waka', 'waka kesiswaan', 'waka_kesiswaan'])) {
                    $roleRaw = 'waka_kesiswaan';
                } elseif (in_array($roleRaw, ['kepsek', 'kepala sekolah', 'kepala_sekolah'])) {
                    $roleRaw = 'kepala_sekolah';
                } elseif (in_array($roleRaw, ['bk', 'guru bk'])) {
                    $roleRaw = 'bk';
                }

                $roleId = $rolesMap[$roleRaw] ?? $defaultRoleId;

                // Simpan User Baru
                User::create([
                    'nip_nik'  => $nipNik ?: null,
                    'nama'     => $nama,
                    'email'    => $email,
                    'password' => Hash::make($row['password'] ?? '123456'),
                    'role_id'  => $roleId,
                ]);

                $insertedCount++;
            }

            // Menyusun Pesan Response
            // Menyusun Pesan Response
            $pesanBerhasil = "Berhasil mengimport {$insertedCount} data pengajar/staf.";

            if (!empty($warnings)) {
                if ($insertedCount > 0) {
                    return redirect()->route('admin.guru.index')
                        ->with('success', $pesanBerhasil)
                        ->with('warning_list', $warnings);
                }
                
                return redirect()->route('admin.guru.index')
                    ->with('warning_list', $warnings);
            }

            return redirect()->route('admin.guru.index')->with('success', $pesanBerhasil);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download Template File Excel Guru
     */
    public function downloadTemplate()
    {
        $filePath = public_path('templates/template_guru.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return back()->with('error', 'File template_guru.xlsx belum tersedia di folder public/templates.');
    }
}