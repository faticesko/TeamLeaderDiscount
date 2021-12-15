<?php

use Slim\Factory\AppFactory;


error_reporting(E_ALL ^ E_DEPRECATED);

define('ROOT_DIR', realpath(__DIR__.'/..'));

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, false);

require __DIR__ . '/../routes/api.php';
$app->run();


