<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Kelola Data Master</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('admin._nav')

            <x-flash-messages />

            <div class="rounded-lg border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-medium text-slate-700">Pengguna</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Nama</th>
                                <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Email</th>
                                <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Peran &amp; Tim</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-3 text-slate-800">{{ $user->name }}</td>
                                    <td class="px-6 py-3 text-slate-600">{{ $user->email }}</td>
                                    <td class="px-6 py-3">
                                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2"
                                            x-data="{ role: '{{ $user->role->value }}' }">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" x-model="role" class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                                @endforeach
                                            </select>
                                            <select name="team_id" x-show="role === 'teknisi'" class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                                <option value="">- Tanpa tim -</option>
                                                @foreach ($teams as $team)
                                                    <option value="{{ $team->id }}" @selected($user->team_id === $team->id)>{{ $team->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-md border border-slate-300 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-3 border-t border-slate-100 px-6 py-4 sm:grid-cols-2">
                    @csrf
                    <input type="text" name="name" required placeholder="Nama"
                        class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <input type="email" name="email" required placeholder="Email"
                        class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <input type="password" name="password" required placeholder="Kata sandi awal" minlength="8"
                        class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <select name="role" class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>
                    <select name="team_id" class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">- Tanpa tim -</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="sm:col-span-2 rounded-md bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Tambah Pengguna
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
