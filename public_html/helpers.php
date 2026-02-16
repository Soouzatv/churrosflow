<?php

function escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $route, array $params = []): void
{
    $query = http_build_query(array_merge(['r' => $route], $params));
    header('Location: index.php?' . $query);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_check(): void
{
    $token = $_POST['_csrf'] ?? '';
    $valid = hash_equals($_SESSION['_csrf'] ?? '', $token);
    if (!$valid) {
        http_response_code(419);
        die('Token CSRF inválido.');
    }
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }

    $message = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $message;
}

function old(string $key, string $default = ''): string
{
    return isset($_SESSION['_old'][$key]) ? (string) $_SESSION['_old'][$key] : $default;
}

function set_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function currency_br(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function percent(float $value): string
{
    return number_format($value, 2, ',', '.') . '%';
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function currentRestaurant(): ?array
{
    return $_SESSION['restaurant'] ?? null;
}

function normalizeHexColor(?string $color, string $fallback): string
{
    $color = strtoupper(trim((string) $color));
    if (preg_match('/^#[0-9A-F]{6}$/', $color)) {
        return $color;
    }
    return strtoupper($fallback);
}

function restaurantTheme(): array
{
    $restaurant = currentRestaurant() ?? [];
    return [
        'primary' => normalizeHexColor($restaurant['primary_color'] ?? null, '#FF7A00'),
        'primary_2' => normalizeHexColor($restaurant['primary_color_2'] ?? null, '#FF9F45'),
        'sidebar_a' => normalizeHexColor($restaurant['sidebar_color_a'] ?? null, '#050505'),
        'sidebar_b' => normalizeHexColor($restaurant['sidebar_color_b'] ?? null, '#151515'),
        'logo_path' => (string) ($restaurant['logo_path'] ?? ''),
    ];
}

function restaurantLogoUrl(): ?string
{
    $logo = trim((string) (restaurantTheme()['logo_path'] ?? ''));
    if ($logo === '') {
        return null;
    }
    return baseUrl($logo);
}

function tenantId(): int
{
    return (int) ($_SESSION['restaurant']['id'] ?? 0);
}

function requireLogin(): void
{
    if (!currentUser()) {
        flash('error', 'Faça login para continuar.');
        redirect('auth/login');
    }
}

function requireTenant(): void
{
    requireLogin();
    if (!tenantId()) {
        flash('error', 'Restaurante inválido.');
        redirect('auth/logout');
    }
}

function isPost(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function baseUrl(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function view(string $template, array $data = [], bool $withLayout = true): void
{
    extract($data);
    $viewFile = VIEWS_PATH . '/' . $template . '.php';

    if (!file_exists($viewFile)) {
        http_response_code(404);
        die('View não encontrada: ' . escape($template));
    }

    if ($withLayout) {
        include VIEWS_PATH . '/layout/header.php';
        include VIEWS_PATH . '/layout/sidebar.php';
        include VIEWS_PATH . '/layout/flash.php';
        include $viewFile;
        include VIEWS_PATH . '/layout/footer.php';
        return;
    }

    include $viewFile;
}

function auditLog(PDO $pdo, int $restaurantId, int $userId, string $action, array $meta = []): void
{
    $stmt = $pdo->prepare('
        INSERT INTO audit_logs (restaurant_id, user_id, action, meta_json, created_at)
        VALUES (:restaurant_id, :user_id, :action, :meta_json, NOW())
    ');
    $stmt->execute([
        ':restaurant_id' => $restaurantId,
        ':user_id' => $userId,
        ':action' => $action,
        ':meta_json' => json_encode($meta, JSON_UNESCAPED_UNICODE),
    ]);
}
