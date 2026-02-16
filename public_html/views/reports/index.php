<div class="mb-4">
    <h2 class="text-2xl font-bold">Relatórios de Lucro</h2>
    <p class="text-slate-600">Análise ideal vs atual e recomendações de margem.</p>
</div>

<?php if (!$ideal_vs_current): ?>
    <div class="card"><p class="text-slate-600">Sem dados para relatório. Cadastre produtos.</p></div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="card">
            <p class="text-sm text-slate-600">Mais lucrativo</p>
            <p class="font-bold"><?= escape($most_profitable['name']) ?></p>
            <p class="text-green-700"><?= escape(currency_br((float) $most_profitable['profit_unit'])) ?>/un</p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-600">Menos lucrativo</p>
            <p class="font-bold"><?= escape($least_profitable['name']) ?></p>
            <p class="<?= (float) $least_profitable['profit_unit'] >= 0 ? 'text-amber-700' : 'text-red-700' ?>">
                <?= escape(currency_br((float) $least_profitable['profit_unit'])) ?>/un
            </p>
        </div>
        <div class="card">
            <p class="text-sm text-slate-600">Margem média real</p>
            <p class="font-bold <?= (float) $avg_margin >= 20 ? 'text-green-700' : 'text-amber-700' ?>">
                <?= escape(percent((float) $avg_margin)) ?>
            </p>
        </div>
    </div>

    <div class="card overflow-x-auto mb-4">
        <h3 class="font-semibold mb-3">Ideal vs Atual</h3>
        <table class="table table-auto w-full text-sm">
            <thead>
            <tr>
                <th>Produto</th>
                <th>Sugerido</th>
                <th>Atual</th>
                <th>Diferença</th>
                <th>Margem real</th>
                <th>Alerta</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ideal_vs_current as $row): ?>
                <tr>
                    <td><?= escape($row['name']) ?></td>
                    <td><?= escape(currency_br((float) $row['suggested_price'])) ?></td>
                    <td><?= escape(currency_br((float) $row['current_price'])) ?></td>
                    <td><?= escape(currency_br((float) $row['price_diff'])) ?></td>
                    <td><?= escape(percent((float) $row['real_margin_pct'])) ?></td>
                    <td>
                        <?php if ((float) $row['profit_unit'] < 0): ?>
                            <span class="badge badge-red">Prejuízo</span>
                        <?php elseif ((float) $row['real_margin_pct'] < 15): ?>
                            <span class="badge badge-amber">Margem baixa</span>
                        <?php else: ?>
                            <span class="badge badge-green">Saudável</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 class="font-semibold mb-2">Recomendações</h3>
        <ul class="m-0 p-0">
            <?php foreach ($recommendations as $rec): ?>
                <li class="mb-2">- <?= escape($rec) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
