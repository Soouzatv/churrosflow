<?php

class UserModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT
                    u.*,
                    r.name AS restaurant_name,
                    r.slug AS restaurant_slug,
                    r.logo_path AS restaurant_logo_path,
                    r.primary_color AS restaurant_primary_color,
                    r.primary_color_2 AS restaurant_primary_color_2,
                    r.sidebar_color_a AS restaurant_sidebar_color_a,
                    r.sidebar_color_b AS restaurant_sidebar_color_b
                FROM users u
                INNER JOIN restaurants r ON r.id = u.restaurant_id
                WHERE u.email = :email
                LIMIT 1
            ');
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            $stmt = $this->pdo->prepare('
                SELECT u.*, r.name AS restaurant_name, r.slug AS restaurant_slug
                FROM users u
                INNER JOIN restaurants r ON r.id = u.restaurant_id
                WHERE u.email = :email
                LIMIT 1
            ');
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();
            return $row ?: null;
        }
    }
}
