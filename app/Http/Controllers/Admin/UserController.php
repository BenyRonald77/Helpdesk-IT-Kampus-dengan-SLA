<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->with('team')->orderBy('name')->get();
        $teams = Team::query()->orderBy('name')->get();

        return view('admin.users.index', [
            'users' => $users,
            'teams' => $teams,
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:'.implode(',', array_map(fn ($r) => $r->value, UserRole::cases()))],
            'team_id' => ['nullable', 'exists:teams,id'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
            'role' => UserRole::from($data['role']),
            'team_id' => $data['team_id'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Pengguna ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:'.implode(',', array_map(fn ($r) => $r->value, UserRole::cases()))],
            'team_id' => ['nullable', 'exists:teams,id'],
        ]);

        $user->role = UserRole::from($data['role']);
        $user->team_id = $user->role === UserRole::Teknisi ? ($data['team_id'] ?? null) : null;
        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Peran pengguna diperbarui.');
    }
}
