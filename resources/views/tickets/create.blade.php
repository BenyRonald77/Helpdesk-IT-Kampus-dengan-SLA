<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Buat Tiket Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="title" value="Judul" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required autofocus
                            value="{{ old('title') }}" placeholder="Contoh: Wifi lab komputer 2 sering putus" />
                    </div>

                    <div>
                        <x-input-label for="category_id" value="Kategori" />
                        <select id="category_id" name="category_id" required
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="priority" value="Prioritas" />
                        <select id="priority" name="priority" required
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->value }}" @selected(old('priority', 'medium') === $priority->value)>
                                    {{ $priority->label() }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">
                            Target waktu penyelesaian ditetapkan otomatis begitu tiket dibuat, sesuai prioritas ini.
                        </p>
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" rows="5" required
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Jelaskan gangguan yang dialami, lokasi, dan sejak kapan terjadi.">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Kirim Tiket
                        </button>
                        <a href="{{ route('tickets.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
