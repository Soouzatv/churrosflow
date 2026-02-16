<?php

class PromotionModel extends BaseModel
{
    public function allByTenant(int $restaurantId, bool $onlyActive = false): array
    {
        $sql = 'SELECT * FROM promotions WHERE restaurant_id = :restaurant_id';
        if ($onlyActive) {
            $sql .= ' AND active = 1';
        }
        $sql .= ' ORDER BY id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':restaurant_id' => $restaurantId]);
        return $stmt->fetchAll();
    }

    public function find(int $id, int $restaurantId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM promotions
            WHERE id = :id AND restaurant_id = :restaurant_id
            LIMIT 1
        ');
        $stmt->execute([':id' => $id, ':restaurant_id' => $restaurantId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(int $restaurantId, array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO promotions (restaurant_id, name, type, rules_json, active, created_at, updated_at)
            VALUES (:restaurant_id, :name, :type, :rules_json, 1, NOW(), NOW())
        ');
        $stmt->execute([
            ':restaurant_id' => $restaurantId,
            ':name' => $data['name'],
            ':type' => $data['type'],
            ':rules_json' => $data['rules_json'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, int $restaurantId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE promotions
            SET name = :name, type = :type, rules_json = :rules_json, updated_at = NOW()
            WHERE id = :id AND restaurant_id = :restaurant_id
        ');
        return $stmt->execute([
            ':id' => $id,
            ':restaurant_id' => $restaurantId,
            ':name' => $data['name'],
            ':type' => $data['type'],
            ':rules_json' => $data['rules_json'],
        ]);
    }

    public function toggle(int $id, int $restaurantId): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE promotions
            SET active = IF(active = 1, 0, 1), updated_at = NOW()
            WHERE id = :id AND restaurant_id = :restaurant_id
        ');
        return $stmt->execute([':id' => $id, ':restaurant_id' => $restaurantId]);
    }

    public function delete(int $id, int $restaurantId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM promotions WHERE id = :id AND restaurant_id = :restaurant_id');
        return $stmt->execute([':id' => $id, ':restaurant_id' => $restaurantId]);
    }
}
