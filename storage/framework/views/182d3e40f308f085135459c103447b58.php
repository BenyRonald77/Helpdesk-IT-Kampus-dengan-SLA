<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Kelola Data Master</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <?php echo $__env->make('admin._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if (isset($component)) { $__componentOriginal5b09c79149dfb771c232996af5f9dae4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b09c79149dfb771c232996af5f9dae4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.flash-messages','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-messages'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5b09c79149dfb771c232996af5f9dae4)): ?>
<?php $attributes = $__attributesOriginal5b09c79149dfb771c232996af5f9dae4; ?>
<?php unset($__attributesOriginal5b09c79149dfb771c232996af5f9dae4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5b09c79149dfb771c232996af5f9dae4)): ?>
<?php $component = $__componentOriginal5b09c79149dfb771c232996af5f9dae4; ?>
<?php unset($__componentOriginal5b09c79149dfb771c232996af5f9dae4); ?>
<?php endif; ?>

            <div class="rounded-lg border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-medium text-slate-700">Kategori Tiket</h3>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->isEmpty()): ?>
                    <p class="px-6 py-6 text-sm text-slate-500">Belum ada kategori. Tambahkan minimal satu supaya pelapor bisa membuat tiket.</p>
                <?php else: ?>
                    <ul class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex flex-wrap items-center justify-between gap-4 px-6 py-3">
                                <form method="POST" action="<?php echo e(route('admin.categories.update', $category)); ?>" class="flex flex-1 flex-wrap items-center gap-2">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <input type="text" name="name" value="<?php echo e($category->name); ?>" class="w-full sm:w-64 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <button type="submit" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        Simpan
                                    </button>
                                    <span class="text-xs text-slate-400"><?php echo e($category->tickets_count); ?> tiket</span>
                                </form>
                                <form method="POST" action="<?php echo e(route('admin.categories.destroy', $category)); ?>"
                                    onsubmit="return confirm('Hapus kategori <?php echo e($category->name); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-500">Hapus</button>
                                </form>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.categories.store')); ?>" class="flex flex-wrap items-center gap-2 border-t border-slate-100 px-6 py-4">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="name" required placeholder="Nama kategori baru"
                        class="w-full sm:w-64 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Tambah Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /tmp/helpdesk/resources/views/admin/categories/index.blade.php ENDPATH**/ ?>