<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div>
        <h2 class="text-2xl font-bold">Produtos</h2>
        <p class="text-slate-600">Custos, margem desejada e preço sugerido.</p>
    </div>
    <a class="btn btn-primary" href="index.php?r=products/create">Novo produto</a>
</div>

<form class="card mb-4" method="get" action="index.php">
    <input type="hidden" name="r" value="products/index">
    <div class="flex gap-2">
        <input class="input" type="text" name="q" value="<?= escape($search) ?>" placeholder="Buscar por nome ou categoria">
        <button class="btn btn-light" type="submit">Buscar</button>
    </div>
</form>

<?php if (!$items): ?>
    <div class="card">
        <p class="text-slate-600">Nenhum produto encontrado.</p>
    </div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="table table-auto w-full text-sm">
            <thead>
            <tr>
                <th>Produto</th>
                <th>Custo total unit</th>
                <th>Preço ideal</th>
                <th>Preço atual</th>
                <th>Lucro unit</th>
                <th>Margem real</th>
                <th>Diferença</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <?php
                $m = $item['metrics'];
                $marginClass = $m['margem_real_no_preco_atual'] >= (float) $item['desired_margin_pct'] ? 'text-green-700' : ($m['margem_real_no_preco_atual'] >= 0 ? 'text-amber-700' : 'text-red-700');
                ?>
                <tr>
                    <td>
                        <strong><?= escape($item['name']) ?></strong><br>
                        <span class="text-xs text-slate-500"><?= escape($item['category']) ?></span>
                    </td>
                    <td><?= escape(currency_br((float) $m['custo_total_unit'])) ?></td>
                    <td><?= escape(currency_br((float) $m['price_ideal'])) ?></td>
                    <td><?= escape(currency_br((float) $item['current_price'])) ?></td>
                    <td class="<?= $m['lucro_unit_no_preco_atual'] >= 0 ? 'text-green-700' : 'text-red-700' ?>">
                        <?= escape(currency_br((float) $m['lucro_unit_no_preco_atual'])) ?>
                    </td>
                    <td class="<?= $marginClass ?>"><?= escape(percent((float) $m['margem_real_no_preco_atual'])) ?></td>
                    <td><?= escape(currency_br((float) $m['diferenca_preco'])) ?></td>
                    <td>
                        <div class="flex gap-2">
                            <a class="btn btn-light" href="index.php?r=products/edit&id=<?= (int) $item['id'] ?>">Editar</a>
                            <form method="post" action="index.php?r=products/delete&id=<?= (int) $item['id'] ?>" data-confirm="Excluir este produto?">
                                <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="flex gap-2 mt-4">
        <?php for ($p = 1; $p <= $pages; $p++): ?>
            <a class="btn <?= $p === $page ? 'btn-primary' : 'btn-light' ?>" href="index.php?r=products/index&q=<?= urlencode($search) ?>&page=<?= $p ?>">
                <?= $p ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
