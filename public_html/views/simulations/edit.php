<div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-bold">Editar Simulação</h2>
    <a class="btn btn-light" href="index.php?r=simulations/index">Voltar</a>
</div>

<div class="card">
    <form method="post" action="index.php?r=simulations/edit&id=<?= (int) $item['id'] ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
        <div class="md:col-span-2">
            <label class="label">Nome do evento</label>
            <input class="input" type="text" name="event_name" value="<?= escape(old('event_name', $item['event_name'])) ?>" required>
        </div>
        <div>
            <label class="label">Produto</label>
            <select class="select" name="product_id" required>
                <?php foreach ($products as $product): ?>
                    <option value="<?= (int) $product['id'] ?>" <?= (int) old('product_id', (string) $item['product_id']) === (int) $product['id'] ? 'selected' : '' ?>>
                        <?= escape($product['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="label">Promoção (opcional)</label>
            <select class="select" name="promotion_id">
                <option value="0">Sem promoção</option>
                <?php foreach ($promotions as $promotion): ?>
                    <option value="<?= (int) $promotion['id'] ?>" <?= (int) old('promotion_id', (string) ($item['promotion_id'] ?? 0)) === (int) $promotion['id'] ? 'selected' : '' ?>>
                        <?= escape($promotion['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="label">Quantidade estimada</label>
            <input class="input" type="number" min="1" name="estimated_qty" value="<?= escape(old('estimated_qty', (string) $item['estimated_qty'])) ?>" required>
        </div>
        <div>
            <label class="label">Custos extras (R$)</label>
            <input class="input" type="number" min="0" step="0.01" name="extra_cost" value="<?= escape(old('extra_cost', (string) $item['extra_cost'])) ?>">
        </div>
        <div>
            <label class="label">Desconto manual (%)</label>
            <input class="input" type="number" min="0" max="100" step="0.01" name="discount_pct" value="<?= escape(old('discount_pct', (string) $item['discount_pct'])) ?>">
        </div>
        <div class="md:col-span-2">
            <label class="label">Observações</label>
            <textarea class="textarea" name="notes"><?= escape(old('notes', (string) ($item['notes'] ?? ''))) ?></textarea>
        </div>
        <div class="md:col-span-2">
            <button class="btn btn-primary" type="submit">Atualizar simulação</button>
        </div>
    </form>
</div>
