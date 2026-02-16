<div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-bold">Nova Simulação</h2>
    <a class="btn btn-light" href="index.php?r=simulations/index">Voltar</a>
</div>

<?php if (!$products): ?>
    <div class="alert alert-error">Cadastre ao menos 1 produto ativo para criar simulações.</div>
<?php else: ?>
    <div class="card">
        <form method="post" action="index.php?r=simulations/create" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
            <div class="md:col-span-2">
                <label class="label">Nome do evento</label>
                <input class="input" type="text" name="event_name" value="<?= escape(old('event_name')) ?>" required>
            </div>
            <div>
                <label class="label">Produto</label>
                <select class="select" name="product_id" required>
                    <option value="">Selecione</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= (int) $product['id'] ?>" <?= old('product_id') == $product['id'] ? 'selected' : '' ?>>
                            <?= escape($product['name']) ?> - Atual <?= escape(currency_br((float) $product['current_price'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label">Promoção (opcional)</label>
                <select class="select" name="promotion_id">
                    <option value="0">Sem promoção</option>
                    <?php foreach ($promotions as $promotion): ?>
                        <option value="<?= (int) $promotion['id'] ?>" <?= old('promotion_id') == $promotion['id'] ? 'selected' : '' ?>>
                            <?= escape($promotion['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label">Quantidade estimada</label>
                <input class="input" type="number" min="1" name="estimated_qty" value="<?= escape(old('estimated_qty', '100')) ?>" required>
            </div>
            <div>
                <label class="label">Custos extras (R$)</label>
                <input class="input" type="number" min="0" step="0.01" name="extra_cost" value="<?= escape(old('extra_cost', '0')) ?>">
            </div>
            <div>
                <label class="label">Desconto manual (%)</label>
                <input class="input" type="number" min="0" max="100" step="0.01" name="discount_pct" value="<?= escape(old('discount_pct', '0')) ?>">
            </div>
            <div class="md:col-span-2">
                <label class="label">Observações</label>
                <textarea class="textarea" name="notes"><?= escape(old('notes')) ?></textarea>
            </div>
            <div class="md:col-span-2">
                <button class="btn btn-primary" type="submit">Salvar simulação</button>
            </div>
        </form>
    </div>
<?php endif; ?>
