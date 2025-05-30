<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';


Request::setTrustedProxies(
    [$_SERVER['REMOTE_ADDR']],
    Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO
);

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
