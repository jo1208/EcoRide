<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

// ✅ N'exécute Dotenv que si on est en développement
if (!isset($_ENV['APP_ENV']) && file_exists(dirname(__DIR__) . '/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

// ✅ Fuseau horaire par défaut
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Paris');
