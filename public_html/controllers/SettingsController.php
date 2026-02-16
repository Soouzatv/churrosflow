<?php

class SettingsController
{
    private RestaurantModel $restaurants;
    private PDO $pdo;

    public function __construct()
    {
        $this->restaurants = new RestaurantModel();
        $this->pdo = getPDO();
    }

    public function index(): void
    {
        requireTenant();
        $restaurantId = tenantId();
        $restaurant = $this->restaurants->findById($restaurantId);
        if (!$restaurant) {
            flash('error', 'Restaurante nao encontrado.');
            redirect('dashboard/index');
        }

        if (isPost()) {
            csrf_check();
            $payload = $this->validate($restaurant, $_POST, $_FILES);
            if ($payload['errors']) {
                set_old($_POST);
                flash('error', implode(' ', $payload['errors']));
                redirect('settings/index');
            }

            try {
                $ok = $this->restaurants->updateProfile($restaurantId, $payload['values']);
                if ($ok) {
                    $_SESSION['restaurant'] = [
                        'id' => $restaurantId,
                        'name' => $payload['values']['name'],
                        'slug' => $payload['values']['slug'],
                        'logo_path' => $payload['values']['logo_path'],
                        'primary_color' => $payload['values']['primary_color'],
                        'primary_color_2' => $payload['values']['primary_color_2'],
                        'sidebar_color_a' => $payload['values']['sidebar_color_a'],
                        'sidebar_color_b' => $payload['values']['sidebar_color_b'],
                    ];
                    auditLog($this->pdo, $restaurantId, (int) currentUser()['id'], 'settings.update', [
                        'name' => $payload['values']['name'],
                        'slug' => $payload['values']['slug'],
                    ]);
                    clear_old();
                    flash('success', 'Configuracoes atualizadas com sucesso.');
                } else {
                    flash('error', 'Nao foi possivel salvar as configuracoes.');
                }
            } catch (Throwable $e) {
                flash('error', 'Falha ao salvar. Se voce acabou de atualizar o sistema, execute SQL: update_restaurant_theme.sql');
            }
            redirect('settings/index');
        }

        view('settings/index', ['restaurant' => $restaurant]);
    }

    private function validate(array $restaurant, array $post, array $files): array
    {
        $name = trim((string) ($post['name'] ?? ''));
        $slug = strtolower(trim((string) ($post['slug'] ?? '')));
        $primaryColor = normalizeHexColor($post['primary_color'] ?? '', '#FF7A00');
        $primaryColor2 = normalizeHexColor($post['primary_color_2'] ?? '', '#FF9F45');
        $sidebarColorA = normalizeHexColor($post['sidebar_color_a'] ?? '', '#050505');
        $sidebarColorB = normalizeHexColor($post['sidebar_color_b'] ?? '', '#151515');
        $logoPath = (string) ($restaurant['logo_path'] ?? '');

        $errors = [];
        if ($name === '') {
            $errors[] = 'Informe o nome do restaurante.';
        }
        if ($slug === '') {
            $errors[] = 'Informe o slug do restaurante.';
        } elseif (!preg_match('/^[a-z0-9-]{3,50}$/', $slug)) {
            $errors[] = 'Slug invalido. Use apenas letras minusculas, numeros e hifen.';
        }

        if (!empty($files['logo']['name'])) {
            $upload = $this->handleLogoUpload($files['logo']);
            if (!$upload['ok']) {
                $errors[] = $upload['error'];
            } else {
                $logoPath = $upload['path'];
            }
        }

        return [
            'errors' => $errors,
            'values' => [
                'name' => $name,
                'slug' => $slug,
                'logo_path' => $logoPath,
                'primary_color' => $primaryColor,
                'primary_color_2' => $primaryColor2,
                'sidebar_color_a' => $sidebarColorA,
                'sidebar_color_b' => $sidebarColorB,
            ],
        ];
    }

    private function handleLogoUpload(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Falha no upload da logo.'];
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > 2 * 1024 * 1024) {
            return ['ok' => false, 'error' => 'A logo deve ter ate 2MB.'];
        }

        $mime = mime_content_type($tmp) ?: '';
        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mime])) {
            return ['ok' => false, 'error' => 'Formato de logo invalido. Use PNG, JPG ou WEBP.'];
        }

        $targetDir = ROOT_PATH . '/storage/logos';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $fileName = 'logo-rest-' . tenantId() . '-' . time() . '.' . $allowed[$mime];
        $targetFile = $targetDir . '/' . $fileName;

        if (!move_uploaded_file($tmp, $targetFile)) {
            return ['ok' => false, 'error' => 'Nao foi possivel salvar a logo no servidor.'];
        }

        return ['ok' => true, 'path' => 'storage/logos/' . $fileName];
    }
}
