<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">Tiket #{{ $ticket->id }}</p>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $ticket->title }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <x-status-badge :status="$ticket->status" />
                <x-sla-badge :ticket="$ticket" />
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Pelapor</dt>
                        <dd class="text-sm text-slate-800">{{ $ticket->requester->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Kategori</dt>
                        <dd class="text-sm text-slate-800">{{ $ticket->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Tim / Teknisi</dt>
                        <dd class="text-sm text-slate-800">
                            {{ $ticket->team?->name ?? 'Belum ditugaskan' }}
                            @if ($ticket->assignee)
                                &middot; {{ $ticket->assignee->name }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Dibuat</dt>
                        <dd class="text-sm text-slate-800">{{ $ticket->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Target penyelesaian (SLA)</dt>
                        <dd class="text-sm text-slate-800">{{ $ticket->sla_due_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Selesai pada</dt>
                        <dd class="text-sm text-slate-800">{{ $ticket->resolved_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <dt class="text-xs font-medium text-slate-500">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-slate-700 whitespace-pre-line">{{ $ticket->description }}</dd>
                </div>
            </div>

            @if ($canSelfAssign || $canManage || $canReassign)
                <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
                    <h3 class="text-sm font-medium text-slate-700">Aksi</h3>

                    @if ($canSelfAssign)
                        <form method="POST" action="{{ route('tickets.self-assign', $ticket) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Ambil Tiket Ini
                            </button>
                        </form>
                    @endif

                    @if ($canManage)
                        <div class="flex flex-wrap items-end gap-4">
                            <form method="POST" action="{{ route('tickets.update-status', $ticket) }}" class="flex items-end gap-2">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <x-input-label for="status" value="Ubah status" class="text-xs" />
                                    <select id="status" name="status" class="mt-1 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Simpan
                                </button>
                            </form>

                            <form method="POST" action="{{ route('tickets.update-priority', $ticket) }}" class="flex items-end gap-2">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <x-input-label for="priority" value="Ubah prioritas" class="text-xs" />
                                    <select id="priority" name="priority" class="mt-1 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority->value }}" @selected($ticket->priority === $priority)>{{ $priority->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Simpan
                                </button>
                            </form>
                        </div>
                        <p class="text-xs text-slate-500">Mengubah prioritas menghitung ulang target SLA dari waktu tiket dibuat.</p>
                    @endif

                    @if ($canReassign)
                        <form method="POST" action="{{ route('tickets.reassign', $ticket) }}" class="flex items-end gap-2">
                            @csrf
                            <div>
                                <x-input-label for="assigned_to" value="Tugaskan ulang ke" class="text-xs" />
                                @if ($reassignTechnicians->isEmpty())
                                    <p class="mt-1 text-sm text-slate-500">Tim ini belum punya teknisi.</p>
                                @else
                                    <select id="assigned_to" name="assigned_to" class="mt-1 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach ($reassignTechnicians as $technician)
                                            <option value="{{ $technician->id }}" @selected($ticket->assigned_to === $technician->id)>
                                                {{ $technician->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            @if ($reassignTechnicians->isNotEmpty())
                                <button type="submit" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Tugaskan Ulang
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            @endif

            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <h3 class="text-sm font-medium text-slate-700">Riwayat &amp; Komentar</h3>

                @if ($ticket->comments->isEmpty())
                    <p class="mt-3 text-sm text-slate-500">Belum ada komentar pada tiket ini.</p>
                @else
                    <ul class="mt-4 space-y-4">
                        @foreach ($ticket->comments as $comment)
                            <li class="border-l-2 border-slate-200 pl-4">
                                <p class="text-sm text-slate-800">{{ $comment->body }}</p>
                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $comment->user->name }} &middot; {{ $comment->created_at->format('d M Y H:i') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($canComment)
                    <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="mt-5">
                        @csrf
                        <label for="body" class="sr-only">Tambah komentar</label>
                        <textarea id="body" name="body" rows="3" required
                            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Tulis komentar atau update penanganan..."></textarea>
                        <button type="submit" class="mt-2 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Kirim Komentar
                        </button>
                    </form>
                @elseif (auth()->user()->isTeknisi() && $ticket->assigned_to === null)
                    <p class="mt-3 text-xs text-slate-500">Ambil tiket ini dahulu untuk bisa menambahkan komentar.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
