<?php

class SimulationModel extends BaseModel
{
    public function allByTenant(int $restaurantId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.*, p.name AS product_name, pr.name AS promotion_name
            FROM event_simulations s
            INNER JOIN products p ON p.id = s.product_id
            LEFT JOIN promotions pr ON pr.id = s.promotion_id
            WHERE s.restaurant_id = :restaurant_id
            ORDER BY s.id DESC
        ');
        $stmt->execute([':restaurant_id' => $restaurantId]);
        return $stmt->fetchAll();
    }

    public function find(int $id, int $restaurantId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.*, p.name AS product_name, p.category, p.cost_production, p.fixed_expenses_unit,
                   p.current_price, p.suggested_price, pr.name AS promotion_name, pr.type AS promotion_type, pr.rules_json
            FROM event_simulations s
            INNER JOIN products p ON p.id = s.product_id
            LEFT JOIN promotions pr ON pr.id = s.promotion_id
            WHERE s.id = :id AND s.restaurant_id = :restaurant_id
            LIMIT 1
        ');
        $stmt->execute([':id' => $id, ':restaurant_id' => $restaurantId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function calculate(array $product, int $qty, float $extraCost, float $discountPct, ?array $promotion): array
    {
        $qty = max(1, $qty);
        $discountPct = max(0, min(100, $discountPct));

        $costTotalUnit = (float) $product['cost_production'] + (float) $product['fixed_expenses_unit'];
        $basePrice = (float) $product['current_price'] > 0 ? (float) $product['current_price'] : (float) $product['suggested_price'];

        $promoCalc = PromotionEngine::apply($basePrice, $qty, $promotion);
        $unitPriceEffective = (float) $promoCalc['unit_price_effective'];

        $grossRevenue = $unitPriceEffective * $qty;
        $revenueAfterDiscount = $grossRevenue * (1 - ($discountPct / 100));
        $totalCost = ($costTotalUnit * $qty) + $extraCost;
        $estimatedProfit = $revenueAfterDiscount - $totalCost;

        $unitContribution = ($unitPriceEffective * (1 - ($discountPct / 100))) - $costTotalUnit;
        $breakEvenQty = (int) ceil($extraCost / max($unitContribution, 0.01));

        return [
            'cost_total_unit' => round($costTotalUnit, 2),
            'base_price' => round($basePrice, 2),
            'used_unit_price' => round($unitPriceEffective, 2),
            'gross_revenue' => round($grossRevenue, 2),
            'revenue_after_discount' => round($revenueAfterDiscount, 2),
            'total_cost' => round($totalCost, 2),
            'estimated_profit' => round($estimatedProfit, 2),
            'break_even_qty' => $breakEvenQty,
            'promotion_summary' => $promoCalc['summary_text'],
            'promotion_discount_value' => $promoCalc['total_discount_value'],
        ];
    }

    public function create(int $restaurantId, array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO event_simulations (
                restaurant_id, event_name, product_id, promotion_id, estimated_qty,
                extra_cost, discount_pct, used_unit_price, gross_revenue, revenue_after_discount,
                total_cost, estimated_profit, break_even_qty, notes, created_at
            ) VALUES (
                :restaurant_id, :event_name, :product_id, :promotion_id, :estimated_qty,
                :extra_cost, :discount_pct, :used_unit_price, :gross_revenue, :revenue_after_discount,
                :total_cost, :estimated_profit, :break_even_qty, :notes, NOW()
            )
        ');
        $stmt->execute([
            ':restaurant_id' => $restaurantId,
            ':event_name' => $data['event_name'],
            ':product_id' => $data['product_id'],
            ':promotion_id' => $data['promotion_id'] ?: null,
            ':estimated_qty' => $data['estimated_qty'],
            ':extra_cost' => $data['extra_cost'],
            ':discount_pct' => $data['discount_pct'],
            ':used_unit_price' => $data['used_unit_price'],
            ':gross_revenue' => $data['gross_revenue'],
            ':revenue_after_discount' => $data['revenue_after_discount'],
            ':total_cost' => $data['total_cost'],
            ':estimated_profit' => $data['estimated_profit'],
            ':break_even_qty' => $data['break_even_qty'],
            ':notes' => $data['notes'] ?: null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, int $restaurantId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE event_simulations
            SET
                event_name = :event_name,
                product_id = :product_id,
                promotion_id = :promotion_id,
                estimated_qty = :estimated_qty,
                extra_cost = :extra_cost,
                discount_pct = :discount_pct,
                used_unit_price = :used_unit_price,
                gross_revenue = :gross_revenue,
                revenue_after_discount = :revenue_after_discount,
                total_cost = :total_cost,
                estimated_profit = :estimated_profit,
                break_even_qty = :break_even_qty,
                notes = :notes
            WHERE id = :id AND restaurant_id = :restaurant_id
        ');
        return $stmt->execute([
            ':id' => $id,
            ':restaurant_id' => $restaurantId,
            ':event_name' => $data['event_name'],
            ':product_id' => $data['product_id'],
            ':promotion_id' => $data['promotion_id'] ?: null,
            ':estimated_qty' => $data['estimated_qty'],
            ':extra_cost' => $data['extra_cost'],
            ':discount_pct' => $data['discount_pct'],
            ':used_unit_price' => $data['used_unit_price'],
            ':gross_revenue' => $data['gross_revenue'],
            ':revenue_after_discount' => $data['revenue_after_discount'],
            ':total_cost' => $data['total_cost'],
            ':estimated_profit' => $data['estimated_profit'],
            ':break_even_qty' => $data['break_even_qty'],
            ':notes' => $data['notes'] ?: null,
        ]);
    }

    public function delete(int $id, int $restaurantId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM event_simulations WHERE id = :id AND restaurant_id = :restaurant_id');
        return $stmt->execute([':id' => $id, ':restaurant_id' => $restaurantId]);
    }
}
