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
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Tiket</h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isPelapor()): ?>
                <a href="<?php echo e(route('tickets.create')); ?>" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Buat Tiket Baru
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <section>
                    <h3 class="text-sm font-medium text-slate-700 mb-2"><?php echo e($group['title']); ?></h3>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['tickets']->isEmpty()): ?>
                        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500">
                            <?php echo e($group['empty_hint']); ?>

                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Judul</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Kategori</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Prioritas</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Status</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">SLA</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">
                                            <span class="sr-only">Aksi</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $group['tickets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="px-4 py-3">
                                                <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="font-medium text-indigo-600 hover:text-indigo-500">
                                                    <?php echo e($ticket->title); ?>

                                                </a>
                                                <p class="text-xs text-slate-400">#<?php echo e($ticket->id); ?> &middot; <?php echo e($ticket->created_at->format('d M Y H:i')); ?></p>
                                            </td>
                                            <td class="px-4 py-3 text-slate-600"><?php echo e($ticket->category->name); ?></td>
                                            <td class="px-4 py-3 text-slate-600"><?php echo e($ticket->priority->label()); ?></td>
                                            <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $ticket->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></td>
                                            <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginalf8537846ecb099bfba89af16a4fcaefa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sla-badge','data' => ['ticket' => $ticket]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sla-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['ticket' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8537846ecb099bfba89af16a4fcaefa)): ?>
<?php $attributes = $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa; ?>
<?php unset($__attributesOriginalf8537846ecb099bfba89af16a4fcaefa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8537846ecb099bfba89af16a4fcaefa)): ?>
<?php $component = $__componentOriginalf8537846ecb099bfba89af16a4fcaefa; ?>
<?php unset($__componentOriginalf8537846ecb099bfba89af16a4fcaefa); ?>
<?php endif; ?></td>
                                            <td class="px-4 py-3 text-right">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($group['show_self_assign'] ?? false) && auth()->user()->can('selfAssign', $ticket)): ?>
                                                    <form method="POST" action="<?php echo e(route('tickets.self-assign', $ticket)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="rounded-md border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                            Ambil Tiket
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="text-xs font-medium text-slate-500 hover:text-slate-700">
                                                        Lihat detail
                                                    </a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /tmp/helpdesk/resources/views/tickets/index.blade.php ENDPATH**/ ?>