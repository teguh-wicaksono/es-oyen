<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OwnerKaryawanController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'owner', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in(['kasir', 'dapur'])],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        User::query()->create($data);

        return redirect()
            ->route('owner.karyawan')
            ->with('success', 'Akun karyawan baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $karyawan): RedirectResponse
    {
        abort_unless($request->user()?->role === 'owner', 403);

        // Prevent editing owner accounts via this controller
        abort_if($karyawan->role === 'owner', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($karyawan->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($karyawan->id)],
            'role' => ['required', Rule::in(['kasir', 'dapur'])],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $karyawan->update($data);

        return redirect()
            ->route('owner.karyawan')
            ->with('success', 'Akun karyawan berhasil diperbarui.');
    }

    public function destroy(Request $request, User $karyawan): RedirectResponse
    {
        abort_unless($request->user()?->role === 'owner', 403);

        // Prevent deleting owner accounts
        abort_if($karyawan->role === 'owner', 403);

        $karyawan->delete();

        return redirect()
            ->route('owner.karyawan')
            ->with('success', 'Akun karyawan berhasil dihapus.');
    }
}
