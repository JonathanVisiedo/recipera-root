<?php

use Ghost\Controllers\AccountController;
use Ghost\Controllers\AdminAuthController;
use Ghost\Controllers\AdminController;
use Ghost\Controllers\ApiController;
use Ghost\Controllers\CarouselController;
use Ghost\Controllers\DashboardController;
use Ghost\Controllers\DisplayController;
use Ghost\Controllers\PageController;
use Ghost\Controllers\ProductController;
use Ghost\Controllers\PromotionController;
use Ghost\Controllers\ProviderController;
use Ghost\Controllers\QuoteController;
use Ghost\Controllers\ServiceController;
use Ghost\Controllers\ShopController;
use Ghost\Controllers\TagController;
use Ghost\Controllers\ActionController;
use Ghost\Controllers\UserController;
use Ghost\Middlewares\AdminAuthMiddleware;
use Ghost\Middlewares\UserAuthMiddleware;
use Ghost\Middlewares\ErrorHandlerMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $settings = $app->getContainer()->get('settings');

    // HOME
    $app->get('/', DisplayController::class . ':index')->setName('index');

    // ERRORS
    $app->get('/404', DisplayController::class . ':error404')->setName('error404');

    // MAIN ROUTES
    $app->group('/', function (Group $app) {
        $app->map(['GET', 'POST'], 'create', DisplayController::class . ':create')->setName('recipe.create');
        $app->get('delete/{slug:[0-9a-z\-]+}', DisplayController::class . ':delete')->setName('recipe.delete');
        $app->get('{slug:[0-9a-z\-]+}', DisplayController::class . ':view')->setName('recipe.view');
    })->add(ErrorHandlerMiddleware::class);

};