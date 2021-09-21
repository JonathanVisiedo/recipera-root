<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__.'/../config/settings.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Create App instance
$app = $container->get(App::class);

// Register middleware
$middleware = require __DIR__ . '/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/routes.php';
$routes($app);


$app->run();