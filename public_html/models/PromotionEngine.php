<?php

class PromotionEngine
{
    public static function apply(float $baseUnitPrice, int $qty, ?array $promotion): array
    {
        $qty = max(1, $qty);
        $baseTotal = $baseUnitPrice * $qty;

        if (!$promotion) {
            return [
                'unit_price_effective' => round($baseUnitPrice, 2),
                'total_price_before_manual_discount' => round($baseTotal, 2),
                'total_discount_value' => 0.0,
                'summary_text' => 'Sem promoção aplicada.',
            ];
        }

        $rules = json_decode((string) ($promotion['rules_json'] ?? '{}'), true) ?: [];
        $type = $promotion['type'] ?? '';
        $total = $baseTotal;
        $summary = 'Promoção aplicada.';

        switch ($type) {
            case 'percent_discount':
                $pct = max(0, min(100, (float) ($rules['discount_pct'] ?? 0)));
                $total = $baseTotal * (1 - $pct / 100);
                $summary = "Desconto de {$pct}% no total.";
                break;

            case 'buy_x_pay_y':
                $buy = max(1, (int) ($rules['buy'] ?? 1));
                $pay = max(1, min($buy, (int) ($rules['pay'] ?? $buy)));
                $fullGroups = intdiv($qty, $buy);
                $remaining = $qty % $buy;
                $payableUnits = ($fullGroups * $pay) + $remaining;
                $total = $payableUnits * $baseUnitPrice;
                $summary = "Leve {$buy} e pague {$pay}.";
                break;

            case 'progressive_discount':
                $tiers = $rules['tiers'] ?? [];
                $bestPct = 0.0;
                foreach ($tiers as $tier) {
                    $minQty = (int) ($tier['min_qty'] ?? 0);
                    $pct = (float) ($tier['discount_pct'] ?? 0);
                    if ($qty >= $minQty && $pct > $bestPct) {
                        $bestPct = $pct;
                    }
                }
                $bestPct = max(0, min(100, $bestPct));
                $total = $baseTotal * (1 - $bestPct / 100);
                $summary = "Desconto progressivo de {$bestPct}% para {$qty} unidades.";
                break;

            case 'combo_fixed_price':
                $bundleQty = max(1, (int) ($rules['bundle_qty'] ?? 1));
                $bundlePrice = max(0, (float) ($rules['bundle_price'] ?? 0));
                $bundles = intdiv($qty, $bundleQty);
                $remaining = $qty % $bundleQty;
                $total = ($bundles * $bundlePrice) + ($remaining * $baseUnitPrice);
                $summary = "Combo {$bundleQty} por " . number_format($bundlePrice, 2, ',', '.');
                break;

            default:
                $summary = 'Tipo de promoção não reconhecido. Preço base mantido.';
                break;
        }

        $unit = $qty > 0 ? ($total / $qty) : $baseUnitPrice;
        $discountValue = max(0, $baseTotal - $total);

        return [
            'unit_price_effective' => round($unit, 2),
            'total_price_before_manual_discount' => round($total, 2),
            'total_discount_value' => round($discountValue, 2),
            'summary_text' => $summary,
        ];
    }
}
