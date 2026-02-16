<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div>
        <h2 class="text-2xl font-bold"><?= escape($item['event_name']) ?></h2>
        <p class="text-slate-600">Produto: <?= escape($item['product_name']) ?></p>
    </div>
    <div class="flex gap-2">
        <a class="btn btn-success" href="index.php?r=pdf/generate&id=<?= (int) $item['id'] ?>">Gerar PDF</a>
        <a class="btn btn-primary" target="_blank" href="<?= escape($waLink) ?>">Enviar via WhatsApp</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    <div class="card">
        <p class="text-sm text-slate-600">Receita bruta</p>
        <p class="text-xl font-bold"><?= escape(currency_br((float) $item['gross_revenue'])) ?></p>
    </div>
    <div class="card">
        <p class="text-sm text-slate-600">Receita com desconto</p>
        <p class="text-xl font-bold"><?= escape(currency_br((float) $item['revenue_after_discount'])) ?></p>
    </div>
    <div class="card">
        <p class="text-sm text-slate-600">Custo total</p>
        <p class="text-xl font-bold"><?= escape(currency_br((float) $item['total_cost'])) ?></p>
    </div>
    <div class="card">
        <p class="text-sm text-slate-600">Lucro estimado</p>
        <p class="text-xl font-bold <?= (float) $item['estimated_profit'] >= 0 ? 'text-green-700' : 'text-red-700' ?>">
            <?= escape(currency_br((float) $item['estimated_profit'])) ?>
        </p>
    </div>
</div>

<div class="card mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><strong>Quantidade:</strong> <?= (int) $item['estimated_qty'] ?></div>
        <div><strong>Preço unitário usado:</strong> <?= escape(currency_br((float) $item['used_unit_price'])) ?></div>
        <div><strong>Ponto de equilíbrio:</strong> <?= (int) $item['break_even_qty'] ?> unidades</div>
        <div><strong>Desconto manual:</strong> <?= escape(percent((float) $item['discount_pct'])) ?></div>
        <div><strong>Custos extras:</strong> <?= escape(currency_br((float) $item['extra_cost'])) ?></div>
        <div><strong>Promoção:</strong> <?= escape($item['promotion_name'] ?? 'Sem promoção') ?></div>
    </div>
    <?php if (!empty($item['notes'])): ?>
        <div class="mt-4">
            <strong>Observações:</strong>
            <p class="text-slate-700"><?= nl2br(escape($item['notes'])) ?></p>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <p class="text-sm">Link público esperado do PDF:</p>
    <a class="text-blue-600" target="_blank" href="<?= escape($pdfPublicUrl) ?>"><?= escape($pdfPublicUrl) ?></a>
</div>
