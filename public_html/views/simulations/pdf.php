<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Simulação <?= escape($simulation['event_name']) ?></title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #0f172a; font-size: 13px; }
        .header { border-bottom: 2px solid #0f172a; margin-bottom: 16px; padding-bottom: 8px; }
        .title { font-size: 24px; font-weight: bold; }
        .sub { color: #475569; font-size: 12px; }
        .grid { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .grid td { border: 1px solid #e2e8f0; padding: 8px; }
        .metric { width: 48%; display: inline-block; margin: 4px 1%; border: 1px solid #e2e8f0; padding: 8px; border-radius: 6px; }
        .metric b { display: block; font-size: 18px; margin-top: 4px; }
        .ok { color: #166534; font-weight: bold; }
        .bad { color: #991b1b; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">CHURROS FLOW</div>
        <div class="sub">Simulação inteligente para eventos</div>
    </div>

    <table class="grid">
        <tr><td><strong>Restaurante</strong></td><td><?= escape($restaurant['name'] ?? '-') ?></td></tr>
        <tr><td><strong>Evento</strong></td><td><?= escape($simulation['event_name']) ?></td></tr>
        <tr><td><strong>Produto</strong></td><td><?= escape($simulation['product_name']) ?> (<?= escape($simulation['category']) ?>)</td></tr>
        <tr><td><strong>Quantidade</strong></td><td><?= (int) $simulation['estimated_qty'] ?></td></tr>
        <tr><td><strong>Promoção</strong></td><td><?= escape($simulation['promotion_name'] ?? 'Sem promoção') ?></td></tr>
    </table>

    <div class="metric">Receita bruta<b><?= escape(currency_br((float) $simulation['gross_revenue'])) ?></b></div>
    <div class="metric">Receita com desconto<b><?= escape(currency_br((float) $simulation['revenue_after_discount'])) ?></b></div>
    <div class="metric">Custo total<b><?= escape(currency_br((float) $simulation['total_cost'])) ?></b></div>
    <div class="metric">Lucro estimado<b><?= escape(currency_br((float) $simulation['estimated_profit'])) ?></b></div>
    <div class="metric">Ponto de equilíbrio<b><?= (int) $simulation['break_even_qty'] ?> unidades</b></div>
    <div class="metric">Preço unitário usado<b><?= escape(currency_br((float) $simulation['used_unit_price'])) ?></b></div>

    <p>
        Status:
        <span class="<?= (float) $simulation['estimated_profit'] >= 0 ? 'ok' : 'bad' ?>">
            <?= (float) $simulation['estimated_profit'] >= 0 ? 'LUCRO' : 'PREJUÍZO' ?>
        </span>
    </p>

    <?php if (!empty($simulation['notes'])): ?>
        <p><strong>Observações:</strong> <?= nl2br(escape((string) $simulation['notes'])) ?></p>
    <?php endif; ?>
</body>
</html>
