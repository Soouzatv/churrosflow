<?php

class DashboardModel extends BaseModel
{
    public function stats(int $restaurantId): array
    {
        $products = (int) $this->scalar(
            'SELECT COUNT(*) FROM products WHERE restaurant_id = :restaurant_id',
            $restaurantId
        );
        $simulations = (int) $this->scalar(
            'SELECT COUNT(*) FROM event_simulations WHERE restaurant_id = :restaurant_id',
            $restaurantId
        );
        $activePromotions = (int) $this->scalar(
            'SELECT COUNT(*) FROM promotions WHERE restaurant_id = :restaurant_id AND active = 1',
            $restaurantId
        );

        $stmt = $this->pdo->prepare('
            SELECT
                COALESCE(SUM(revenue_after_discount), 0) AS revenue_month,
                COALESCE(SUM(estimated_profit), 0) AS profit_month
            FROM event_simulations
            WHERE restaurant_id = :restaurant_id
              AND YEAR(created_at) = YEAR(CURDATE())
              AND MONTH(created_at) = MONTH(CURDATE())
        ');
        $stmt->execute([':restaurant_id' => $restaurantId]);
        $month = $stmt->fetch() ?: ['revenue_month' => 0, 'profit_month' => 0];

        return [
            'products' => $products,
            'simulations' => $simulations,
            'active_promotions' => $activePromotions,
            'revenue_month' => (float) $month['revenue_month'],
            'profit_month' => (float) $month['profit_month'],
        ];
    }

    private function scalar(string $sql, int $restaurantId): string
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':restaurant_id' => $restaurantId]);
        return (string) $stmt->fetchColumn();
    }
}
