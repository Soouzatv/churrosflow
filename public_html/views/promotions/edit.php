<?php $rules = json_decode((string) $promotion['rules_json'], true) ?: []; ?>
<div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-bold">Editar Promoção</h2>
    <a class="btn btn-light" href="index.php?r=promotions/index">Voltar</a>
</div>

<div class="card">
    <form method="post" action="index.php?r=promotions/edit&id=<?= (int) $promotion['id'] ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
        <div class="md:col-span-2">
            <label class="label">Nome</label>
            <input class="input" type="text" name="name" value="<?= escape(old('name', $promotion['name'])) ?>" required>
        </div>
        <div class="md:col-span-2">
            <label class="label">Tipo</label>
            <select class="select" name="type" required>
                <?php $type = old('type', $promotion['type']); ?>
                <option value="percent_discount" <?= $type === 'percent_discount' ? 'selected' : '' ?>>Desconto %</option>
                <option value="buy_x_pay_y" <?= $type === 'buy_x_pay_y' ? 'selected' : '' ?>>Leve X pague Y</option>
                <option value="progressive_discount" <?= $type === 'progressive_discount' ? 'selected' : '' ?>>Desconto progressivo</option>
                <option value="combo_fixed_price" <?= $type === 'combo_fixed_price' ? 'selected' : '' ?>>Combo preço fixo</option>
            </select>
        </div>
        <div>
            <label class="label">Desconto % (tipo 1)</label>
            <input class="input" type="number" step="0.01" name="discount_pct" value="<?= escape(old('discount_pct', (string) ($rules['discount_pct'] ?? 10))) ?>">
        </div>
        <div>
            <label class="label">Leve X (tipo 2)</label>
            <input class="input" type="number" name="buy_qty" value="<?= escape(old('buy_qty', (string) ($rules['buy'] ?? 3))) ?>">
        </div>
        <div>
            <label class="label">Pague Y (tipo 2)</label>
            <input class="input" type="number" name="pay_qty" value="<?= escape(old('pay_qty', (string) ($rules['pay'] ?? 2))) ?>">
        </div>
        <?php $tiers = $rules['tiers'] ?? [[], []]; ?>
        <div>
            <label class="label">Tier1 min qty (tipo 3)</label>
            <input class="input" type="number" name="tier1_qty" value="<?= escape(old('tier1_qty', (string) ($tiers[0]['min_qty'] ?? 10))) ?>">
        </div>
        <div>
            <label class="label">Tier1 desconto % (tipo 3)</label>
            <input class="input" type="number" step="0.01" name="tier1_discount" value="<?= escape(old('tier1_discount', (string) ($tiers[0]['discount_pct'] ?? 5))) ?>">
        </div>
        <div>
            <label class="label">Tier2 min qty (tipo 3)</label>
            <input class="input" type="number" name="tier2_qty" value="<?= escape(old('tier2_qty', (string) ($tiers[1]['min_qty'] ?? 30))) ?>">
        </div>
        <div>
            <label class="label">Tier2 desconto % (tipo 3)</label>
            <input class="input" type="number" step="0.01" name="tier2_discount" value="<?= escape(old('tier2_discount', (string) ($tiers[1]['discount_pct'] ?? 10))) ?>">
        </div>
        <div>
            <label class="label">Qtd do combo (tipo 4)</label>
            <input class="input" type="number" name="bundle_qty" value="<?= escape(old('bundle_qty', (string) ($rules['bundle_qty'] ?? 2))) ?>">
        </div>
        <div>
            <label class="label">Preço do combo (tipo 4)</label>
            <input class="input" type="number" step="0.01" name="bundle_price" value="<?= escape(old('bundle_price', (string) ($rules['bundle_price'] ?? 29.90))) ?>">
        </div>
        <div class="md:col-span-2">
            <button class="btn btn-primary" type="submit">Atualizar promoção</button>
        </div>
    </form>
</div>
