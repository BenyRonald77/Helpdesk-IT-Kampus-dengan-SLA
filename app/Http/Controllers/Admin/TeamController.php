<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::query()->with(['supervisor', 'technicians'])->orderBy('name')->get();
        $supervisors = User::query()->where('role', UserRole::Supervisor->value)->orderBy('name')->get();

        return view('admin.teams.index', compact('teams', 'supervisors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:teams,name'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
        ]);

        Team::create($data);

        return redirect()->route('admin.teams.index')->with('status', 'Tim ditambahkan.');
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:teams,name,'.$team->id],
            'supervisor_id' => ['nullable', 'exists:users,id'],
        ]);

        $team->update($data);

        return redirect()->route('admin.teams.index')->with('status', 'Tim diperbarui.');
    }
}
