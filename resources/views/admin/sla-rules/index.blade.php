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
                    <h3 class="text-sm font-medium text-slate-700">Aturan SLA per Prioritas</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Mengubah target di sini hanya berlaku untuk tiket baru atau tiket yang prioritasnya diganti setelah ini.
                        Tiket yang sudah punya target SLA tidak berubah, supaya riwayat tetap stabil.
                    </p>
                </div>

                <ul class="divide-y divide-slate-100">
                    @foreach ($rules as $rule)
                        <li class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.sla-rules.update', $rule) }}" class="flex flex-wrap items-end gap-4">
                                @csrf
                                @method('PATCH')
                                <div class="w-28">
                                    <span class="text-sm font-medium text-slate-800">{{ $rule->priority->label() }}</span>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500" for="response_minutes_{{ $rule->id }}">Target respons (menit)</label>
                                    <input type="number" min="1" id="response_minutes_{{ $rule->id }}" name="response_minutes"
                                        value="{{ $rule->response_minutes }}"
                                        class="mt-1 block w-32 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500" for="resolution_minutes_{{ $rule->id }}">Target resolusi (menit)</label>
                                    <input type="number" min="1" id="resolution_minutes_{{ $rule->id }}" name="resolution_minutes"
                                        value="{{ $rule->resolution_minutes }}"
                                        class="mt-1 block w-32 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <button type="submit" class="rounded-md border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Simpan
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
