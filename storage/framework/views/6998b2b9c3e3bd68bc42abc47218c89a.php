<nav class="flex gap-4 border-b border-slate-200 text-sm">
    <a href="<?php echo e(route('admin.categories.index')); ?>" class="border-b-2 px-1 py-2 <?php echo e(request()->routeIs('admin.categories.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'); ?>">
        Kategori
    </a>
    <a href="<?php echo e(route('admin.sla-rules.index')); ?>" class="border-b-2 px-1 py-2 <?php echo e(request()->routeIs('admin.sla-rules.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'); ?>">
        Aturan SLA
    </a>
    <a href="<?php echo e(route('admin.teams.index')); ?>" class="border-b-2 px-1 py-2 <?php echo e(request()->routeIs('admin.teams.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'); ?>">
        Tim
    </a>
    <a href="<?php echo e(route('admin.users.index')); ?>" class="border-b-2 px-1 py-2 <?php echo e(request()->routeIs('admin.users.*') ? 'border-indigo-500 font-medium text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'); ?>">
        Pengguna
    </a>
</nav>
<?php /**PATH /tmp/helpdesk/resources/views/admin/_nav.blade.php ENDPATH**/ ?>