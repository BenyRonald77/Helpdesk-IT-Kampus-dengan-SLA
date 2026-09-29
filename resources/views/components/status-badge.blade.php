@props(['status'])

@php
    $classes = match ($status->value) {
        'open' => 'bg-slate-100 text-slate-700 border-slate-200',
        'in_progress' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'escalated' => 'bg-red-50 text-red-700 border-red-200',
        'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'closed' => 'bg-slate-100 text-slate-500 border-slate-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-xs font-medium $classes"]) }}>
    {{ $status->label() }}
</span>
