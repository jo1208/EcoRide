<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// ✅ Corrige la gestion des proxys pour Heroku avec Symfony 7
Request::setTrustedProxies(
    [$_SERVER['REMOTE_ADDR']],
    Request::HEADER_FORWARDED
);

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
