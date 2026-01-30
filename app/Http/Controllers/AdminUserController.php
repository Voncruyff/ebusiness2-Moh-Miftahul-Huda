<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $role = (string) $request->query('role', '');
        $sort = (string) $request->query('sort', 'rank'); // rank|name|email|role|created
        $dir  = strtolower((string) $request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $query = User::query();

        // search
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // filter role
        if ($role === 'admin' || $role === 'user') {
            $query->where('role', $role);
        }

        // admin selalu di atas
        $query->orderByRaw("role = 'admin' DESC");

        // sorting tambahan
        switch ($sort) {
            case 'name':
                $query->orderBy('name', $dir);
                break;
            case 'email':
                $query->orderBy('email', $dir);
                break;
            case 'role':
                $query->orderBy('role', $dir);
                break;
            case 'created':
                $query->orderBy('created_at', $dir);
                break;
            case 'rank':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $users = $query->paginate(10)->appends($request->query());

        return view('dashboard.manageuser', compact('users', 'q', 'role', 'sort', 'dir'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:180', 'unique:users,email'],
            'role'     => ['required', 'in:user,admin'],
            'password' => ['required', 'min:8'],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:180', 'unique:users,email,' . $user->id],
            'role'     => ['required', 'in:user,admin'],
            'password' => ['nullable', 'min:8'],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kamu tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kamu tidak bisa mengubah role akun sendiri.');
        }

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', 'Role user berhasil diubah.');
    }
}
