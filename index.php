<?php

require __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/db.php';
require_once(__DIR__."/extensions/jsonresult.php");
require_once(__DIR__."/middlewares/authMiddleware.php");
require_once(__DIR__."/middlewares/onlyAdminMiddleware.php");

use Slim\Factory\AppFactory;


$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Include route files
require __DIR__.'/routes/auth.php';
require __DIR__.'/routes/users.php';
require __DIR__.'/routes/absences.php';
require __DIR__.'/routes/positions.php';

$app->run();
