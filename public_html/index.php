<?php

declare(strict_types=1);

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_name('churrosflow_session');
session_start();

require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';

spl_autoload_register(function (string $className): void {
    $paths = [
        CONTROLLERS_PATH . '/' . $className . '.php',
        MODELS_PATH . '/' . $className . '.php',
    ];

    foreach ($paths as $file) {
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

function simpleRouter(string $route): void
{
    if ($route === '') {
        $route = currentUser() ? 'dashboard/index' : 'auth/login';
    }

    [$controllerPart, $actionPart] = array_pad(explode('/', $route, 2), 2, 'index');
    $controllerName = ucfirst($controllerPart) . 'Controller';
    $action = $actionPart ?: 'index';

    if (!class_exists($controllerName)) {
        http_response_code(404);
        die('Controller não encontrado.');
    }

    $controller = new $controllerName();
    if (!method_exists($controller, $action)) {
        http_response_code(404);
        die('Ação não encontrada.');
    }

    $controller->$action();
}

$route = trim((string) ($_GET['r'] ?? ''), '/');
simpleRouter($route);
