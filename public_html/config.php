<?php

date_default_timezone_set('America/Sao_Paulo');

define('APP_NAME', 'CHURROS FLOW');
define('DEBUG', false);

define('DB_HOST', 'localhost');
define('DB_NAME', 'rona7402_churrosflow');
define('DB_USER', 'rona7402_churrosflow'); // se seu usuário MySQL for esse
define('DB_PASS', 'kfldlkfd01k10kJ!');
define('DB_PORT', 3306);


// Ajuste este valor para o seu dominio/pasta em producao.
define('BASE_URL', 'https://temartes.com/ChurrosFlow/public_html');
define('PDF_PUBLIC_PATH', '/pdf');

define('ROOT_PATH', __DIR__);
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('MODELS_PATH', ROOT_PATH . '/models');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('VENDOR_AUTOLOAD', ROOT_PATH . '/vendor/autoload.php');
define('PDF_DIR', ROOT_PATH . '/pdf');
define('EXPORT_DIR', ROOT_PATH . '/storage/exports');
