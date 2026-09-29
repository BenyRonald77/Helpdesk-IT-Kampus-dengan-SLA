<nav class="flex gap-4 border-b border-slate-200 text-sm">
    <a href="{{ route('admin.categories.index') }}" class="border-b-2 px-1 py-2 {{ request()->routeIs('admin.categories.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
        Kategori
    </a>
    <a href="{{ route('admin.sla-rules.index') }}" class="border-b-2 px-1 py-2 {{ request()->routeIs('admin.sla-rules.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
        Aturan SLA
    </a>
    <a href="{{ route('admin.teams.index') }}" class="border-b-2 px-1 py-2 {{ request()->routeIs('admin.teams.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
        Tim
    </a>
    <a href="{{ route('admin.users.index') }}" class="border-b-2 px-1 py-2 {{ request()->routeIs('admin.users.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
        Pengguna
    </a>
</nav>
