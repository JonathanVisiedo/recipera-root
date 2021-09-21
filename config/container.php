<?php

use Ghost\Middlewares\FlashMessageMiddleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Twig\Extension\DebugExtension;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    Session::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['session'];

        if(PHP_SAPI === 'cli') return new Session(new MockArraySessionStorage());

        return new Session(new NativeSessionStorage($settings));

    },
    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        return new PDO($dsn, $username, $password, $flags);
    },
    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer($container->get(App::class), Twig::class);
    },
    // Twig templates
    Twig::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $twigSettings = $settings['twig'];

        $options = $twigSettings['options'];
        $options['cache'] = $options['cache_enabled'] ? $options['cache_path'] : false;

        $twig = Twig::create($twigSettings['paths'], $options);
        $twig->addExtension(new DebugExtension());

        // GLOBALS
        $env = $twig->getEnvironment();
        if (ENVIRONMENT === 'PROD') {
            $env->enableDebug();
        }
        $env->addGlobal('public_env', ENVIRONMENT);
        $env->addGlobal('User', $container->get(Session::class)->get('userAuthSession'));
        $env->addGlobal('Admin', $container->get(Session::class)->get('adminAuthSession'));
        $env->addGlobal('flash', $container->get(Session::class)->get('flash'));
        $env->addGlobal('login_flash', $container->get(Session::class)->get('login_flash'));

        return $twig;
    },
    Logger::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['logger'];

        $log = new Logger($settings['name']);
        $log->pushHandler(new StreamHandler($settings['path'] . 'debug_' . $settings['filename'], LOGGER::DEBUG, '', $settings['file_permission']));
        $log->pushHandler(new StreamHandler($settings['path'] . 'info_' . $settings['filename'], LOGGER::INFO, '', $settings['file_permission']));
        $log->pushHandler(new StreamHandler($settings['path'] . 'error_' . $settings['filename'], LOGGER::ERROR, '', $settings['file_permission']));

        return $log;
    },
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    }


];