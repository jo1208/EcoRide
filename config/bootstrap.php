<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (!isset($_SERVER['APP_ENV'])) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

// ✅ Définir le fuseau horaire ici
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Paris');
