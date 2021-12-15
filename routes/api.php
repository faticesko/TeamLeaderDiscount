<?php

use Slim\Factory\AppFactory;
use App\Controllers\DiscountController;


/** @var AppFactory $app */

$app->post('/order', DiscountController::class);
