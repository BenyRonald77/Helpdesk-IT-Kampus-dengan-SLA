<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['ticket']));

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

foreach (array_filter((['ticket']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
    <span <?php echo e($attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-md border px-2 py-0.5 text-xs font-medium $classes"])); ?>>
        <span class="h-1.5 w-1.5 rounded-full <?php echo e($dotClasses); ?>" aria-hidden="true"></span>
        <?php echo e($label); ?>

    </span>
<?php else: ?>
    <span <?php echo e($attributes->merge(['class' => 'text-xs text-slate-400'])); ?>>Tanpa target SLA</span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /tmp/helpdesk/resources/views/components/sla-badge.blade.php ENDPATH**/ ?>