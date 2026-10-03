<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['status']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = match ($status->value) {
        'open' => 'bg-slate-100 text-slate-700 border-slate-200',
        'in_progress' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'escalated' => 'bg-red-50 text-red-700 border-red-200',
        'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'closed' => 'bg-slate-100 text-slate-500 border-slate-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
?>

<span <?php echo e($attributes->merge(['class' => "inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-xs font-medium $classes"])); ?>>
    <?php echo e($status->label()); ?>

</span>
<?php /**PATH /tmp/helpdesk/resources/views/components/status-badge.blade.php ENDPATH**/ ?>