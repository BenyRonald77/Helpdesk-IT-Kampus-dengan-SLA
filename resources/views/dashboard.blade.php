<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($stats as $stat)
                    <div class="rounded-lg border border-slate-200 bg-white p-5">
                        <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <h3 class="text-sm font-medium text-slate-700">Mulai dari sini</h3>
                <div class="mt-3 flex flex-wrap gap-3">
                    @if (auth()->user()->isPelapor())
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Buat Tiket Baru
                        </a>
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Lihat Tiket Saya
                        </a>
                    @elseif (auth()->user()->isTeknisi())
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Lihat Antrian Tiket
                        </a>
                    @elseif (auth()->user()->isSupervisor())
                        <a href="{{ route('escalations.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Lihat Tiket Dieskalasi
                        </a>
                        <a href="{{ route('report.index') }}" class="inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Laporan Performa Tim
                        </a>
                    @else
                        <a href="{{ route('report.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Laporan Performa
                        </a>
                        <a href="{{ route('escalations.index') }}" class="inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Tiket Dieskalasi
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Kelola Data Master
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
