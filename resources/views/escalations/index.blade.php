<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Tiket Dieskalasi</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            @if (! $hasAnyTeam)
                <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500">
                    Anda belum ditetapkan sebagai supervisor tim manapun. Hubungi admin untuk mengaitkan akun Anda ke sebuah tim.
                </div>
            @elseif ($tickets->isEmpty())
                <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500">
                    Tidak ada tiket yang sedang dieskalasi saat ini. Tiket akan muncul di sini otomatis saat lewat target SLA dan belum diselesaikan.
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Tiket</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Tim</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Teknisi Saat Ini</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Dieskalasi Sejak</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">SLA</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Tugaskan Ulang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($tickets as $ticket)
                                @php $lastEscalation = $ticket->escalations->last(); @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                            {{ $ticket->title }}
                                        </a>
                                        <p class="text-xs text-slate-400">#{{ $ticket->id }} &middot; {{ $ticket->requester->name }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $ticket->team?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $ticket->assignee?->name ?? 'Belum ada' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $lastEscalation?->escalated_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3"><x-sla-badge :ticket="$ticket" /></td>
                                    <td class="px-4 py-3">
                                        @php $technicians = $techniciansByTeam->get($ticket->team_id, collect()); @endphp
                                        @if ($technicians->isEmpty())
                                            <span class="text-xs text-slate-400">Tidak ada teknisi di tim ini</span>
                                        @else
                                            <form method="POST" action="{{ route('tickets.reassign', $ticket) }}" class="flex items-center gap-2">
                                                @csrf
                                                <select name="assigned_to" class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                                    @foreach ($technicians as $technician)
                                                        <option value="{{ $technician->id }}" @selected($ticket->assigned_to === $technician->id)>
                                                            {{ $technician->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="rounded-md border border-slate-300 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    Tugaskan
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
