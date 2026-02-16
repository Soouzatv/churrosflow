<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= escape(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= escape(baseUrl('assets/css/tailwind.min.css')) ?>">
    <link rel="stylesheet" href="<?= escape(baseUrl('assets/css/app.css')) ?>">
</head>
<body class="login-screen">
<div class="login-backdrop"></div>
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="card login-card w-full max-w-md">
        <h1 class="text-2xl font-bold mb-2">CHURROS FLOW</h1>
        <p class="text-sm text-slate-600 mb-4">Acesse sua conta do restaurante</p>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-error mb-4"><?= escape($error) ?></div>
        <?php endif; ?>
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success mb-4"><?= escape($success) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?r=auth/login" class="flex flex-col gap-3">
            <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
            <div>
                <label class="label">E-mail</label>
                <input class="input" type="email" name="email" value="<?= escape(old('email')) ?>" required>
            </div>
            <div>
                <label class="label">Senha</label>
                <input class="input" type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar no painel</button>
        </form>

        <p class="text-xs text-slate-500 mt-4">
            Usuario dev: <strong>admin@churrosflow.local</strong> / senha: <strong>admin123</strong>
        </p>
    </div>
</div>
</body>
</html>
