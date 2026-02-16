<?php
$theme = restaurantTheme();
$logoUrl = restaurantLogoUrl();
?>
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">Configuracoes do Restaurante</h2>
        <p class="text-slate-600">Edite dados, envie logo e personalize o tema do seu painel.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="card">
        <h3 class="font-semibold mb-3">Perfil</h3>
        <form method="post" action="index.php?r=settings/index" enctype="multipart/form-data" class="flex flex-col gap-3">
            <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">

            <div>
                <label class="label">Nome do restaurante</label>
                <input class="input" type="text" name="name" value="<?= escape(old('name', (string) $restaurant['name'])) ?>" required>
            </div>

            <div>
                <label class="label">Slug</label>
                <input class="input" type="text" name="slug" value="<?= escape(old('slug', (string) $restaurant['slug'])) ?>" required>
            </div>

            <div>
                <label class="label">Logo (PNG/JPG/WEBP, max 2MB)</label>
                <input class="input" type="file" name="logo" accept=".png,.jpg,.jpeg,.webp">
            </div>

            <?php if ($logoUrl): ?>
                <div class="logo-preview-box">
                    <p class="text-xs text-slate-500 mb-2">Logo atual</p>
                    <img src="<?= escape($logoUrl) ?>" alt="Logo" class="brand-logo-preview">
                </div>
            <?php endif; ?>

            <h3 class="font-semibold mt-2">Tema (preto + laranja customizavel)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <div class="color-meta">
                        <label class="label m-0">Cor primaria</label>
                        <span id="theme_primary_value" class="color-value"><?= escape(strtoupper(old('primary_color', $theme['primary']))) ?></span>
                    </div>
                    <input id="theme_primary" data-value-id="theme_primary_value" class="input theme-picker" type="color" name="primary_color" value="<?= escape(old('primary_color', $theme['primary'])) ?>">
                </div>
                <div>
                    <div class="color-meta">
                        <label class="label m-0">Cor primaria 2</label>
                        <span id="theme_primary_2_value" class="color-value"><?= escape(strtoupper(old('primary_color_2', $theme['primary_2']))) ?></span>
                    </div>
                    <input id="theme_primary_2" data-value-id="theme_primary_2_value" class="input theme-picker" type="color" name="primary_color_2" value="<?= escape(old('primary_color_2', $theme['primary_2'])) ?>">
                </div>
                <div>
                    <div class="color-meta">
                        <label class="label m-0">Fundo menu 1</label>
                        <span id="theme_sidebar_a_value" class="color-value"><?= escape(strtoupper(old('sidebar_color_a', $theme['sidebar_a']))) ?></span>
                    </div>
                    <input id="theme_sidebar_a" data-value-id="theme_sidebar_a_value" class="input theme-picker" type="color" name="sidebar_color_a" value="<?= escape(old('sidebar_color_a', $theme['sidebar_a'])) ?>">
                </div>
                <div>
                    <div class="color-meta">
                        <label class="label m-0">Fundo menu 2</label>
                        <span id="theme_sidebar_b_value" class="color-value"><?= escape(strtoupper(old('sidebar_color_b', $theme['sidebar_b']))) ?></span>
                    </div>
                    <input id="theme_sidebar_b" data-value-id="theme_sidebar_b_value" class="input theme-picker" type="color" name="sidebar_color_b" value="<?= escape(old('sidebar_color_b', $theme['sidebar_b'])) ?>">
                </div>
            </div>

            <div class="flex gap-2 mt-2">
                <button type="button" id="apply_theme_preview" class="btn btn-light">Aplicar preview</button>
                <button type="submit" class="btn btn-primary">Salvar configuracoes</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 class="font-semibold mb-3">Preview do Tema</h3>
        <div class="theme-preview">
            <div class="theme-preview-sidebar">
                <div class="theme-preview-logo">LOGO</div>
                <div class="theme-preview-item active">Dashboard</div>
                <div class="theme-preview-item">Produtos</div>
                <div class="theme-preview-item">Simulacoes</div>
            </div>
            <div class="theme-preview-content">
                <div class="theme-preview-topbar"></div>
                <div class="theme-preview-card">
                    <div class="theme-preview-btn"></div>
                    <div class="theme-preview-line"></div>
                    <div class="theme-preview-line short"></div>
                </div>
            </div>
        </div>
        <p class="text-sm text-slate-600 mt-3">Todos os dados e cores sao aplicados por restaurante (multi-tenant).</p>
    </div>
</div>
