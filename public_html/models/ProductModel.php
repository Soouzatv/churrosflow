<?php

class ProductModel extends BaseModel
{
    public static function calculateMetrics(array $data): array
    {
        $costProduction = (float) ($data['cost_production'] ?? 0);
        $fixedExpenses = (float) ($data['fixed_expenses_unit'] ?? 0);
        $desiredMargin = (float) ($data['desired_margin_pct'] ?? 0);
        $currentPrice = (float) ($data['current_price'] ?? 0);

        $totalCost = $costProduction + $fixedExpenses;
        $divisor = 1 - ($desiredMargin / 100);
        $idealPrice = $divisor > 0 ? ($totalCost / $divisor) : 0;
        $profitCurrent = $currentPrice - $totalCost;
        $realMargin = $currentPrice > 0 ? (($profitCurrent / $currentPrice) * 100) : 0;
        $priceDiff = $currentPrice - $idealPrice;

        return [
            'custo_total_unit' => round($totalCost, 2),
            'price_ideal' => round($idealPrice, 2),
            'lucro_unit_no_preco_atual' => round($profitCurrent, 2),
            'margem_real_no_preco_atual' => round($realMargin, 2),
            'diferenca_preco' => round($priceDiff, 2),
        ];
    }

    public function countByTenant(int $restaurantId, string $search = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE restaurant_id = :restaurant_id';
        $params = [':restaurant_id' => $restaurantId];
        if ($search !== '') {
            $sql .= ' AND (name LIKE :search OR category LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function paginateByTenant(int $restaurantId, string $search, int $limit, int $offset): array
    {
        $sql = '
            SELECT *
            FROM products
            WHERE restaurant_id = :restaurant_id
        ';
        $params = [':restaurant_id' => $restaurantId];
        if ($search !== '') {
            $sql .= ' AND (name LIKE :search OR category LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY id DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function allByTenant(int $restaurantId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM products
            WHERE restaurant_id = :restaurant_id AND active = 1
            ORDER BY name ASC
        ');
        $stmt->execute([':restaurant_id' => $restaurantId]);
        return $stmt->fetchAll();
    }

    public function find(int $id, int $restaurantId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM products WHERE id = :id AND restaurant_id = :restaurant_id LIMIT 1
        ');
        $stmt->execute([
            ':id' => $id,
            ':restaurant_id' => $restaurantId,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data, int $restaurantId): int
    {
        $metrics = self::calculateMetrics($data);
        $stmt = $this->pdo->prepare('
            INSERT INTO products (
                restaurant_id, name, category, cost_production, fixed_expenses_unit,
                desired_margin_pct, suggested_price, current_price, active, created_at, updated_at
            ) VALUES (
                :restaurant_id, :name, :category, :cost_production, :fixed_expenses_unit,
                :desired_margin_pct, :suggested_price, :current_price, 1, NOW(), NOW()
            )
        ');
        $stmt->execute([
            ':restaurant_id' => $restaurantId,
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':cost_production' => $data['cost_production'],
            ':fixed_expenses_unit' => $data['fixed_expenses_unit'],
            ':desired_margin_pct' => $data['desired_margin_pct'],
            ':suggested_price' => $metrics['price_ideal'],
            ':current_price' => $data['current_price'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, int $restaurantId, array $data): bool
    {
        $metrics = self::calculateMetrics($data);
        $stmt = $this->pdo->prepare('
            UPDATE products
            SET
                name = :name,
                category = :category,
                cost_production = :cost_production,
                fixed_expenses_unit = :fixed_expenses_unit,
                desired_margin_pct = :desired_margin_pct,
                suggested_price = :suggested_price,
                current_price = :current_price,
                updated_at = NOW()
            WHERE id = :id AND restaurant_id = :restaurant_id
        ');
        return $stmt->execute([
            ':id' => $id,
            ':restaurant_id' => $restaurantId,
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':cost_production' => $data['cost_production'],
            ':fixed_expenses_unit' => $data['fixed_expenses_unit'],
            ':desired_margin_pct' => $data['desired_margin_pct'],
            ':suggested_price' => $metrics['price_ideal'],
            ':current_price' => $data['current_price'],
        ]);
    }

    public function delete(int $id, int $restaurantId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id AND restaurant_id = :restaurant_id');
        return $stmt->execute([':id' => $id, ':restaurant_id' => $restaurantId]);
    }
}
