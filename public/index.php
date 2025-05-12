<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// ✅ Ajout pour corriger le CSRF sur Heroku (proxy trust)
Request::setTrustedProxies(
    ['127.0.0.1', '::1'],
    Request::HEADER_X_FORWARDED_ALL
);

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
