<section class="mb-6">
    <h2 class="text-2xl font-bold">Dashboard Inteligente</h2>
    <p class="text-slate-600">Visão rápida de performance do mês atual.</p>
</section>

<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <p class="text-sm text-slate-600">Produtos cadastrados</p>
        <p class="text-2xl font-bold"><?= (int) $stats['products'] ?></p>
    </div>
    <div class="card">
        <p class="text-sm text-slate-600">Simulações</p>
        <p class="text-2xl font-bold"><?= (int) $stats['simulations'] ?></p>
    </div>
    <div class="card">
        <p class="text-sm text-slate-600">Promoções ativas</p>
        <p class="text-2xl font-bold"><?= (int) $stats['active_promotions'] ?></p>
    </div>
    <div class="card">
        <p class="text-sm text-slate-600">Lucro projetado do mês</p>
        <p class="text-2xl font-bold <?= $stats['profit_month'] >= 0 ? 'text-green-700' : 'text-red-700' ?>">
            <?= escape(currency_br((float) $stats['profit_month'])) ?>
        </p>
    </div>
</section>

<section class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="card">
        <h3 class="font-semibold mb-2">Faturamento projetado do mês</h3>
        <p class="text-2xl font-bold"><?= escape(currency_br((float) $stats['revenue_month'])) ?></p>
    </div>
    <div class="card">
        <h3 class="font-semibold mb-2">Resumo de leitura</h3>
        <p class="text-sm text-slate-600">Use Produtos para atualizar custos e Simulações para prever resultado de eventos.</p>
    </div>
</section>
