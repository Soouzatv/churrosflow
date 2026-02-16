<?php $route = (string) ($_GET['r'] ?? 'dashboard/index'); ?>
<aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 p-4 hidden md:block">
    <div class="brand-block mb-6">
        <?php if ($logo = restaurantLogoUrl()): ?>
            <img src="<?= escape($logo) ?>" alt="Logo do restaurante" class="brand-logo mb-2">
        <?php endif; ?>
        <h2 class="text-white text-xl font-bold">CHURROS FLOW</h2>
        <p class="text-sm text-slate-300">Painel multi-tenant</p>
    </div>
    <nav class="flex flex-col gap-2">
        <a class="sidebar-link <?= str_starts_with($route, 'dashboard/') ? 'active' : '' ?>" href="index.php?r=dashboard/index">Dashboard</a>
        <a class="sidebar-link <?= str_starts_with($route, 'products/') ? 'active' : '' ?>" href="index.php?r=products/index">Produtos</a>
        <a class="sidebar-link <?= str_starts_with($route, 'promotions/') ? 'active' : '' ?>" href="index.php?r=promotions/index">Promocoes</a>
        <a class="sidebar-link <?= str_starts_with($route, 'simulations/') ? 'active' : '' ?>" href="index.php?r=simulations/index">Simulacoes</a>
        <a class="sidebar-link <?= str_starts_with($route, 'reports/') ? 'active' : '' ?>" href="index.php?r=reports/index">Relatorios</a>
        <a class="sidebar-link <?= str_starts_with($route, 'settings/') ? 'active' : '' ?>" href="index.php?r=settings/index">Configuracoes</a>
        <a class="sidebar-link" href="index.php?r=auth/logout">Sair</a>
    </nav>
</aside>
