<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Tiket</h2>
            @if (auth()->user()->isPelapor())
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Buat Tiket Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <x-flash-messages />

            @foreach ($groups as $group)
                <section>
                    <h3 class="text-sm font-medium text-slate-700 mb-2">{{ $group['title'] }}</h3>

                    @if ($group['tickets']->isEmpty())
                        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500">
                            {{ $group['empty_hint'] }}
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Judul</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Kategori</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Prioritas</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Status</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">SLA</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">
                                            <span class="sr-only">Aksi</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($group['tickets'] as $ticket)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                                    {{ $ticket->title }}
                                                </a>
                                                <p class="text-xs text-slate-400">#{{ $ticket->id }} &middot; {{ $ticket->created_at->format('d M Y H:i') }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-slate-600">{{ $ticket->category->name }}</td>
                                            <td class="px-4 py-3 text-slate-600">{{ $ticket->priority->label() }}</td>
                                            <td class="px-4 py-3"><x-status-badge :status="$ticket->status" /></td>
                                            <td class="px-4 py-3"><x-sla-badge :ticket="$ticket" /></td>
                                            <td class="px-4 py-3 text-right">
                                                @if (($group['show_self_assign'] ?? false) && auth()->user()->can('selfAssign', $ticket))
                                                    <form method="POST" action="{{ route('tickets.self-assign', $ticket) }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-md border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                            Ambil Tiket
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-xs font-medium text-slate-500 hover:text-slate-700">
                                                        Lihat detail
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
</x-app-layout>
