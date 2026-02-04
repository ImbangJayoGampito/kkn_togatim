<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(isset($title) ? $title . ' - ' . config('app.name') : config('app.name')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200">

    
    <?php if (isset($component)) { $__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58 = $attributes; } ?>
<?php $component = Mary\View\Components\Nav::resolve(['sticky' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Nav::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:hidden']); ?>
         <?php $__env->slot('brand', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-brand'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppBrand::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $attributes = $__attributesOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__attributesOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $component = $__componentOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__componentOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <label for="main-drawer" class="lg:hidden me-3">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-bars-3'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'cursor-pointer']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </label>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58)): ?>
<?php $attributes = $__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58; ?>
<?php unset($__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58)): ?>
<?php $component = $__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58; ?>
<?php unset($__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal11da67fd6f50ab34ca1b98cbdd145132 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132 = $attributes; } ?>
<?php $component = Mary\View\Components\Main::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('main'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Main::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        
         <?php $__env->slot('sidebar', null, ['drawer' => 'main-drawer','collapsible' => true,'class' => 'bg-base-100 lg:bg-inherit']); ?> 

            
            <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-brand'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppBrand::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'px-5 pt-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $attributes = $__attributesOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__attributesOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $component = $__componentOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__componentOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal254139bd69d0def79ecb6c40efbc400d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal254139bd69d0def79ecb6c40efbc400d = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSeparator::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSeparator::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $attributes = $__attributesOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $component = $__componentOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__componentOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044 = $attributes; } ?>
<?php $component = Mary\View\Components\Menu::resolve(['activateByRoute' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Menu::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Menu Utama','icon' => 'o-user','link' => '/'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Korong','icon' => 'o-map-pin','link' => route('korong.index')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Produk','icon' => 'o-shopping-bag','link' => route('products.index')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'UMKM','icon' => 'o-building-storefront','link' => route('business.index')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user = auth()->user()): ?>

                    <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Dashboard','icon' => 'o-home','link' => '/dashboard'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hasVerifiedEmail()): ?>
                        

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
                            <?php if (isset($component)) { $__componentOriginald82092fa13795886cb51cb7dc7d7b48e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSub::resolve(['title' => 'Administration','icon' => 'o-cog'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-sub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Pengguna','icon' => 'o-users','link' => '/admin/users'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Peran','icon' => 'o-user-group','link' => '/admin/roles'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Izin','icon' => 'o-key','link' => '/admin/permissions'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $attributes = $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $component = $__componentOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        
                        <div class="p-4 mt-2 text-sm bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                            <p>Please verify your email to access all features.</p>
                            <a href="<?php echo e(route('verification.notice')); ?>" class="text-blue-600 hover:underline">Verify
                                Now</a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Profile','icon' => 'o-user','link' => '/profile'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Log out','icon' => 'o-arrow-right-start-on-rectangle','link' => route('logout')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="-mx-2 !-my-2 rounded p-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Tamu
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if (isset($component)) { $__componentOriginal96fdff35e0ddfd31ddf96c499b591184 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96fdff35e0ddfd31ddf96c499b591184 = $attributes; } ?>
<?php $component = Mary\View\Components\ThemeToggle::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('theme-toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\ThemeToggle::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn btn-circle btn-ghost btn-sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal96fdff35e0ddfd31ddf96c499b591184)): ?>
<?php $attributes = $__attributesOriginal96fdff35e0ddfd31ddf96c499b591184; ?>
<?php unset($__attributesOriginal96fdff35e0ddfd31ddf96c499b591184); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal96fdff35e0ddfd31ddf96c499b591184)): ?>
<?php $component = $__componentOriginal96fdff35e0ddfd31ddf96c499b591184; ?>
<?php unset($__componentOriginal96fdff35e0ddfd31ddf96c499b591184); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-arrow-right-start-on-rectangle','tooltipLeft' => 'Log-in','noWireNavigate' => true,'link' => '/login'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-circle btn-ghost btn-xs']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal254139bd69d0def79ecb6c40efbc400d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal254139bd69d0def79ecb6c40efbc400d = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSeparator::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSeparator::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $attributes = $__attributesOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $component = $__componentOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__componentOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user = auth()->user()): ?>
                    
                    <?php if (isset($component)) { $__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418 = $attributes; } ?>
<?php $component = Mary\View\Components\ListItem::resolve(['item' => $user,'value' => 'name','subValue' => 'email','noSeparator' => true,'noHover' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('list-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\ListItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '-mx-2 !-my-2 rounded']); ?>
                         <?php $__env->slot('actions', null, []); ?> 
                            <div class="flex items-center gap-2">
                                <?php if (isset($component)) { $__componentOriginal96fdff35e0ddfd31ddf96c499b591184 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96fdff35e0ddfd31ddf96c499b591184 = $attributes; } ?>
<?php $component = Mary\View\Components\ThemeToggle::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('theme-toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\ThemeToggle::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn btn-circle btn-ghost btn-sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal96fdff35e0ddfd31ddf96c499b591184)): ?>
<?php $attributes = $__attributesOriginal96fdff35e0ddfd31ddf96c499b591184; ?>
<?php unset($__attributesOriginal96fdff35e0ddfd31ddf96c499b591184); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal96fdff35e0ddfd31ddf96c499b591184)): ?>
<?php $component = $__componentOriginal96fdff35e0ddfd31ddf96c499b591184; ?>
<?php unset($__componentOriginal96fdff35e0ddfd31ddf96c499b591184); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-arrow-right-start-on-rectangle','tooltipLeft' => 'Log-out','noWireNavigate' => true,'link' => '/logout'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-circle btn-ghost btn-xs']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                            </div>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418)): ?>
<?php $attributes = $__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418; ?>
<?php unset($__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418)): ?>
<?php $component = $__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418; ?>
<?php unset($__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418); ?>
<?php endif; ?>
                <?php else: ?>
                    
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044)): ?>
<?php $attributes = $__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044; ?>
<?php unset($__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044)): ?>
<?php $component = $__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044; ?>
<?php unset($__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>

        
         <?php $__env->slot('content', null, []); ?> 
            <?php echo e($slot); ?>

         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132)): ?>
<?php $attributes = $__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132; ?>
<?php unset($__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11da67fd6f50ab34ca1b98cbdd145132)): ?>
<?php $component = $__componentOriginal11da67fd6f50ab34ca1b98cbdd145132; ?>
<?php unset($__componentOriginal11da67fd6f50ab34ca1b98cbdd145132); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal2aca76be1376419dfd37220f36011753 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aca76be1376419dfd37220f36011753 = $attributes; } ?>
<?php $component = Mary\View\Components\Toast::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Toast::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $attributes = $__attributesOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__attributesOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $component = $__componentOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__componentOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>
    <footer class="pt-12 border-t border-gray-800 text-center text-gray-500 text-sm">
        <p>© <?php echo e(date('Y')); ?> Nagari Toboh Gadang Timur. Semua data bersifat informatif.</p>
        <p class="mt-2">Sumber data: Pemerintahan Nagari Toboh Gadang Timur</p>
    </footer>
</body>

</html>
<?php /**PATH D:\WebDev\kkn_togatim\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>