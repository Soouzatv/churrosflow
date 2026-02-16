<?php

class RestaurantModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM restaurants WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateProfile(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE restaurants
            SET
                name = :name,
                slug = :slug,
                logo_path = :logo_path,
                primary_color = :primary_color,
                primary_color_2 = :primary_color_2,
                sidebar_color_a = :sidebar_color_a,
                sidebar_color_b = :sidebar_color_b
            WHERE id = :id
        ');

        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':logo_path' => $data['logo_path'],
            ':primary_color' => $data['primary_color'],
            ':primary_color_2' => $data['primary_color_2'],
            ':sidebar_color_a' => $data['sidebar_color_a'],
            ':sidebar_color_b' => $data['sidebar_color_b'],
        ]);
    }
}
