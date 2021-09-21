<?php

use Ghost\Middlewares\ErrorHandlerMiddleware;
use Ghost\Middlewares\FlashMessageMiddleware;
use Ghost\Middlewares\SessionMiddleware;
use http\Client\Request;
use Middlewares\TrailingSlash;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\ErrorMiddleware;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {

    $container = $app->getContainer();

    $app->add(SessionMiddleware::class);
    $app->add(FlashMessageMiddleware::class);
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();
    $app->add(new TrailingSlash(false));

    // Catch exceptions and errors
    $app->add(ErrorHandlerMiddleware::class);
    $app->add(ErrorMiddleware::class);

};