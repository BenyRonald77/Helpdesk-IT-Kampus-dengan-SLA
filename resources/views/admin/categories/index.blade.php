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
                    <h3 class="text-sm font-medium text-slate-700">Kategori Tiket</h3>
                </div>

                @if ($categories->isEmpty())
                    <p class="px-6 py-6 text-sm text-slate-500">Belum ada kategori. Tambahkan minimal satu supaya pelapor bisa membuat tiket.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($categories as $category)
                            <li class="flex flex-wrap items-center justify-between gap-4 px-6 py-3">
                                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex flex-1 flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="name" value="{{ $category->name }}" class="w-full sm:w-64 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <button type="submit" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        Simpan
                                    </button>
                                    <span class="text-xs text-slate-400">{{ $category->tickets_count }} tiket</span>
                                </form>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                    onsubmit="return confirm('Hapus kategori {{ $category->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-500">Hapus</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-wrap items-center gap-2 border-t border-slate-100 px-6 py-4">
                    @csrf
                    <input type="text" name="name" required placeholder="Nama kategori baru"
                        class="w-full sm:w-64 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Tambah Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
