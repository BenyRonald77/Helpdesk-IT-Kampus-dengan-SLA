<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Kelola Data Master</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('admin._nav')

            <x-flash-messages />

            <div class="rounded-lg border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-medium text-slate-700">Tim Teknisi</h3>
                </div>

                @if ($teams->isEmpty())
                    <p class="px-6 py-6 text-sm text-slate-500">Belum ada tim.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($teams as $team)
                            <li class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.teams.update', $team) }}" class="flex flex-wrap items-end gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="text-xs text-slate-500" for="name_{{ $team->id }}">Nama tim</label>
                                        <input type="text" id="name_{{ $team->id }}" name="name" value="{{ $team->name }}"
                                            class="mt-1 block w-56 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500" for="supervisor_id_{{ $team->id }}">Supervisor</label>
                                        <select id="supervisor_id_{{ $team->id }}" name="supervisor_id"
                                            class="mt-1 block w-56 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">- Belum ada -</option>
                                            @foreach ($supervisors as $supervisor)
                                                <option value="{{ $supervisor->id }}" @selected($team->supervisor_id === $supervisor->id)>
                                                    {{ $supervisor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="rounded-md border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        Simpan
                                    </button>
                                    <span class="text-xs text-slate-400">{{ $team->technicians->count() }} teknisi</span>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('admin.teams.store') }}" class="flex flex-wrap items-end gap-3 border-t border-slate-100 px-6 py-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500" for="new_name">Nama tim baru</label>
                        <input type="text" id="new_name" name="name" required
                            class="mt-1 block w-56 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-slate-500" for="new_supervisor_id">Supervisor</label>
                        <select id="new_supervisor_id" name="supervisor_id"
                            class="mt-1 block w-56 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">- Belum ada -</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Tambah Tim
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
