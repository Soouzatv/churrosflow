<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">Promoções Inteligentes</h2>
        <p class="text-slate-600">Gerencie regras e veja impacto de lucro.</p>
    </div>
    <a class="btn btn-primary" href="index.php?r=promotions/create">Nova promoção</a>
</div>

<?php if (!$hasSample): ?>
    <div class="alert alert-info mb-4">Cadastre ao menos 1 produto ativo para habilitar preview de lucro da promoção.</div>
<?php endif; ?>

<?php if (!$items): ?>
    <div class="card"><p class="text-slate-600">Nenhuma promoção cadastrada.</p></div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="table table-auto w-full text-sm">
            <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Rules JSON</th>
                <th>Lucro real estimado</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <?php $pv = $preview[$item['id']] ?? null; ?>
                <tr>
                    <td><?= escape($item['name']) ?></td>
                    <td><span class="badge badge-blue"><?= escape($item['type']) ?></span></td>
                    <td>
                        <span class="badge <?= (int) $item['active'] === 1 ? 'badge-green' : 'badge-amber' ?>">
                            <?= (int) $item['active'] === 1 ? 'Ativa' : 'Inativa' ?>
                        </span>
                    </td>
                    <td><code><?= escape($item['rules_json']) ?></code></td>
                    <td>
                        <?php if ($pv): ?>
                            <span class="<?= $pv['loss'] ? 'text-red-700' : 'text-green-700' ?>">
                                <?= escape(currency_br((float) $pv['profit'])) ?>/un
                            </span>
                            <div>
                                <span class="badge <?= $pv['loss'] ? 'badge-red' : 'badge-green' ?>">
                                    <?= $pv['loss'] ? 'ALERTA PREJUÍZO' : 'LUCRO OK' ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-500">Sem preview</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a class="btn btn-light" href="index.php?r=promotions/edit&id=<?= (int) $item['id'] ?>">Editar</a>
                            <form method="post" action="index.php?r=promotions/toggle&id=<?= (int) $item['id'] ?>">
                                <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
                                <button class="btn btn-neutral" type="submit">Ativar/Desativar</button>
                            </form>
                            <form method="post" action="index.php?r=promotions/delete&id=<?= (int) $item['id'] ?>" data-confirm="Excluir esta promoção?">
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
