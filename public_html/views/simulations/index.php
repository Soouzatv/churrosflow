<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">Simulações de Eventos</h2>
        <p class="text-slate-600">Previsão de receita, custo, lucro e ponto de equilíbrio.</p>
    </div>
    <a class="btn btn-primary" href="index.php?r=simulations/create">Nova simulação</a>
</div>

<?php if (!$items): ?>
    <div class="card"><p class="text-slate-600">Nenhuma simulação cadastrada ainda.</p></div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="table table-auto w-full text-sm">
            <thead>
            <tr>
                <th>Evento</th>
                <th>Produto</th>
                <th>Qtd</th>
                <th>Receita líquida</th>
                <th>Custo total</th>
                <th>Lucro estimado</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= escape($item['event_name']) ?></td>
                    <td>
                        <?= escape($item['product_name']) ?>
                        <?php if (!empty($item['promotion_name'])): ?>
                            <br><span class="text-xs text-slate-500">Promo: <?= escape($item['promotion_name']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= (int) $item['estimated_qty'] ?></td>
                    <td><?= escape(currency_br((float) $item['revenue_after_discount'])) ?></td>
                    <td><?= escape(currency_br((float) $item['total_cost'])) ?></td>
                    <td class="<?= (float) $item['estimated_profit'] >= 0 ? 'text-green-700' : 'text-red-700' ?>">
                        <?= escape(currency_br((float) $item['estimated_profit'])) ?>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a class="btn btn-light" href="index.php?r=simulations/view&id=<?= (int) $item['id'] ?>">Ver</a>
                            <a class="btn btn-light" href="index.php?r=simulations/edit&id=<?= (int) $item['id'] ?>">Editar</a>
                            <form method="post" action="index.php?r=simulations/delete&id=<?= (int) $item['id'] ?>" data-confirm="Excluir esta simulação?">
                                <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
                                <button class="btn btn-danger" type="submit">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
