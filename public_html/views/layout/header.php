<?php
$route = (string) ($_GET['r'] ?? 'dashboard/index');
$theme = restaurantTheme();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= escape(baseUrl('assets/css/tailwind.min.css')) ?>">
    <link rel="stylesheet" href="<?= escape(baseUrl('assets/css/app.css')) ?>">
    <style>
        :root {
            --primary: <?= escape($theme['primary']) ?>;
            --primary-2: <?= escape($theme['primary_2']) ?>;
            --sidebar-a: <?= escape($theme['sidebar_a']) ?>;
            --sidebar-b: <?= escape($theme['sidebar_b']) ?>;
        }
    </style>
</head>
<body class="app-theme">
<div id="menu-overlay" class="menu-overlay hidden md:hidden"></div>
<div class="min-h-screen app-shell">
    <header class="topbar sticky top-0 z-40 px-4 py-3 md:pl-64">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button id="menu-toggle" class="btn btn-light md:hidden" type="button">Menu</button>
                <?php if ($logo = restaurantLogoUrl()): ?>
                    <img src="<?= escape($logo) ?>" alt="Logo" class="topbar-logo">
                <?php endif; ?>
                <div>
                    <h1 class="text-lg font-bold"><?= escape(APP_NAME) ?></h1>
                    <p class="text-sm text-slate-600">MicroSaaS de Precificacao Inteligente</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold"><?= escape(currentRestaurant()['name'] ?? '-') ?></div>
                <div class="text-xs text-slate-500"><?= escape(currentUser()['name'] ?? '-') ?></div>
            </div>
        </div>
    </header>
    <main class="md:pl-64 main-content">
