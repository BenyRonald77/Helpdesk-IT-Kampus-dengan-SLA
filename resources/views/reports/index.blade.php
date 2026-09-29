<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Laporan Performa Bulanan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <form method="GET" action="{{ route('report.index') }}" class="flex flex-wrap items-end gap-3 rounded-lg border border-slate-200 bg-white p-4">
                <div>
                    <x-input-label for="month" value="Bulan" class="text-xs" />
                    <select id="month" name="month" class="mt-1 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" @selected($month === $m)>
                                {{ \Illuminate\Support\Carbon::create(2000, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="year" value="Tahun" class="text-xs" />
                    <select id="year" name="year" class="mt-1 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach (range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Tampilkan
                </button>
            </form>

            @if ($teams->isEmpty())
                <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500">
                    Belum ada tim yang bisa dilaporkan. Buat tim dan kaitkan teknisi lewat halaman Kelola &gt; Tim.
                </div>
            @endif

            @foreach ($teams as $team)
                <div class="rounded-lg border border-slate-200 bg-white">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-6 py-4">
                        <h3 class="text-sm font-semibold text-slate-800">{{ $team['team']->name }}</h3>
                        <dl class="flex gap-6 text-sm">
                            <div>
                                <dt class="text-xs text-slate-500">Tiket ditangani</dt>
                                <dd class="font-medium text-slate-800">{{ $team['tickets_handled'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Rata-rata resolusi</dt>
                                <dd class="font-medium text-slate-800">{{ $team['avg_resolution_hours'] }} jam</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Breach SLA</dt>
                                <dd class="font-medium text-slate-800">{{ $team['sla_breaches'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Tepat waktu</dt>
                                <dd class="font-medium text-slate-800">{{ $team['on_time_percentage'] }}%</dd>
                            </div>
                        </dl>
                    </div>

                    @if (empty($team['technicians']))
                        <p class="px-6 py-6 text-sm text-slate-500">Tim ini belum punya teknisi.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Teknisi</th>
                                        <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Tiket Ditangani</th>
                                        <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Rata-rata Resolusi</th>
                                        <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Breach SLA</th>
                                        <th scope="col" class="px-6 py-2 text-left font-medium text-slate-500">Persentase Tepat Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($team['technicians'] as $stat)
                                        <tr>
                                            <td class="px-6 py-3 text-slate-800">{{ $stat['technician']->name }}</td>
                                            <td class="px-6 py-3 text-slate-600">
                                                @if ($stat['tickets_handled'] === 0)
                                                    <span class="text-slate-400">0 (belum ada tiket selesai bulan ini)</span>
                                                @else
                                                    {{ $stat['tickets_handled'] }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-slate-600">{{ $stat['avg_resolution_hours'] }} jam</td>
                                            <td class="px-6 py-3 text-slate-600">{{ $stat['sla_breaches'] }}</td>
                                            <td class="px-6 py-3 text-slate-600">{{ $stat['on_time_percentage'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
