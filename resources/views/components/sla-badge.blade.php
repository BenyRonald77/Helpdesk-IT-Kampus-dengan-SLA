@props(['ticket'])

@php
    $color = $ticket->slaColorStatus();
    $label = $ticket->remainingLabel();

    $classes = match ($color) {
        'on_time' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'approaching' => 'bg-amber-50 text-amber-700 border-amber-200',
        'breached' => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-slate-100 text-slate-500 border-slate-200',
    };

    $dotClasses = match ($color) {
        'on_time' => 'bg-emerald-600',
        'approaching' => 'bg-amber-600',
        'breached' => 'bg-red-600',
        default => 'bg-slate-400',
    };
@endphp

@if($label)
    <span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-md border px-2 py-0.5 text-xs font-medium $classes"]) }}>
        <span class="h-1.5 w-1.5 rounded-full {{ $dotClasses }}" aria-hidden="true"></span>
        {{ $label }}
    </span>
@else
    <span {{ $attributes->merge(['class' => 'text-xs text-slate-400']) }}>Tanpa target SLA</span>
@endif
