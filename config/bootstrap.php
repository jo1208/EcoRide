<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

// ✅ N'exécute Dotenv que si on est en développement
if (!getenv('APP_ENV') && !isset($_ENV['APP_ENV']) && !isset($_SERVER['APP_ENV'])) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

// ✅ Fuseau horaire par défaut
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Paris');
