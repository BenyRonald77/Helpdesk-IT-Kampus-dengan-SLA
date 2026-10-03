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
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Tiket Dieskalasi</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $hasAnyTeam): ?>
                <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500">
                    Anda belum ditetapkan sebagai supervisor tim manapun. Hubungi admin untuk mengaitkan akun Anda ke sebuah tim.
                </div>
            <?php elseif($tickets->isEmpty()): ?>
                <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500">
                    Tidak ada tiket yang sedang dieskalasi saat ini. Tiket akan muncul di sini otomatis saat lewat target SLA dan belum diselesaikan.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Tiket</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Tim</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Teknisi Saat Ini</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Dieskalasi Sejak</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">SLA</th>
                                <th scope="col" class="px-4 py-2 text-left font-medium text-slate-500">Tugaskan Ulang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $lastEscalation = $ticket->escalations->last(); ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="font-medium text-indigo-600 hover:text-indigo-500">
                                            <?php echo e($ticket->title); ?>

                                        </a>
                                        <p class="text-xs text-slate-400">#<?php echo e($ticket->id); ?> &middot; <?php echo e($ticket->requester->name); ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600"><?php echo e($ticket->team?->name ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?php echo e($ticket->assignee?->name ?? 'Belum ada'); ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?php echo e($lastEscalation?->escalated_at?->format('d M Y H:i') ?? '-'); ?></td>
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
                                    <td class="px-4 py-3">
                                        <?php $technicians = $techniciansByTeam->get($ticket->team_id, collect()); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($technicians->isEmpty()): ?>
                                            <span class="text-xs text-slate-400">Tidak ada teknisi di tim ini</span>
                                        <?php else: ?>
                                            <form method="POST" action="<?php echo e(route('tickets.reassign', $ticket)); ?>" class="flex items-center gap-2">
                                                <?php echo csrf_field(); ?>
                                                <select name="assigned_to" class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $technician): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($technician->id); ?>" <?php if($ticket->assigned_to === $technician->id): echo 'selected'; endif; ?>>
                                                            <?php echo e($technician->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </select>
                                                <button type="submit" class="rounded-md border border-slate-300 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    Tugaskan
                                                </button>
                                            </form>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /tmp/helpdesk/resources/views/escalations/index.blade.php ENDPATH**/ ?>