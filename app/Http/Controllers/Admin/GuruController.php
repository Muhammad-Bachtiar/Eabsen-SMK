<?php

namespace App\Http\Controllers\Admin;

use Spatie\SimpleExcel\SimpleExcelReader;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $roleIds = Role::whereIn('nama_role', ['guru', 'bk'])->pluck('id');
        $gurus   = User::whereIn('role_id', $roleIds)->with('role')->get();
        
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        $roles = Role::whereIn('nama_role', ['guru', 'bk'])->get();
        return view('admin.guru.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_nik'  => 'required|unique:users,nip_nik',
            'nama'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'nullable|in:guru,bk'
        ]);

        $targetRole = $request->input('role', 'guru');
        $role       = Role::where('nama_role', $targetRole)->first();

        User::create([
            'role_id'      => $role->id,
            'nip_nik'      => $request->nip_nik,
            'nama'         => $request->nama,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'status_aktif' => 1
        ]);

        return redirect()->route('admin.guru.index')->with('success', 'Data Akun Staf Pengajar berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $guru  = User::findOrFail($id);
        $roles = Role::whereIn('nama_role', ['guru', 'bk'])->get();
        return view('admin.guru.edit', compact('guru', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $guru = User::findOrFail($id);

        $request->validate([
            'nip_nik'  => 'required|unique:users,nip_nik,' . $guru->id,
            'nama'     => 'required',
            'email'    => 'required|email|unique:users,email,' . $guru->id,
            'password' => 'nullable|min:6',
            'role'     => 'nullable|in:guru,bk'
        ]);

        $data = [
            'nip_nik' => $request->nip_nik,
            'nama'    => $request->nama,
            'email'   => $request->email,
        ];

        if ($request->filled('role')) {
            $role = Role::where('nama_role', $request->role)->first();
            if ($role) {
                $data['role_id'] = $role->id;
            }
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data Akun Staf Pengajar berhasil diperbarui.');
    }

    public function downloadTemplate()
    {
        $filePath = public_path('template/Template_Import_Guru.xlsx');
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return back()->with('error', 'File template belum tersedia di server.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $rows = SimpleExcelReader::create($file->getRealPath(), $file->getClientOriginalExtension())->getRows();

            $roleGuru = Role::where('nama_role', 'LIKE', '%guru%')->where('nama_role', 'NOT LIKE', '%bk%')->first();
            $roleBk   = Role::where('nama_role', 'LIKE', '%bk%')->first();

            $rows->each(function(array $row) use ($roleGuru, $roleBk) {
                if (!empty($row['email'])) {
                    $inputRole = strtolower(trim($row['role'] ?? 'guru'));
                    
                    $assignedRoleId = (in_array($inputRole, ['bk', 'guru bk'])) 
                        ? ($roleBk->id ?? $roleGuru->id) 
                        : ($roleGuru->id ?? null);

                    if ($assignedRoleId) {
                        User::updateOrCreate(
                            ['email' => $row['email']],
                            [
                                'nip_nik'  => $row['nip_nik'] ?? null,
                                'nama'     => $row['nama'],
                                'password' => Hash::make($row['password'] ?? '12345678'),
                                'role_id'  => $assignedRoleId
                            ]
                        );
                    }
                }
            });

            return redirect()->route('admin.guru.index')->with('success', 'Data Guru & BK berhasil diimport!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function destroy(User $guru)
    {
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Data Akun Guru berhasil dihapus.');
    }
}