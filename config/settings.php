<?php


// Timezone
use Monolog\Logger;

date_default_timezone_set('Europe/Brussels');

// Settings
$settings = [];


// Path settings
$settings['environment'] = ENVIRONMENT; // || DEV
$settings['admin'] = 'recipera';
$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/resources/tmp';
$settings['public'] = $settings['root'] . '/public';

// Error reporting for production
if ($settings['environment'] == 'PROD') {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Error Handling Middleware settings
$settings['error'] = [

    // Should be set to false in production
    'display_error_details' => ($settings['environment'] === 'PROD' ? false : true),

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];

$settings['db'] = [
    'driver' => 'mysql',
    'host' => 'mysql',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_general_ci',
    'database' => 'db',
    'username' => 'user',
    'password' => 'password',
    'flags' => [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
];


$settings['twig'] = [
    // Template paths
    'paths' => [
        $settings['root'] . '/src/Views',
    ],
    // Twig environment options
    'options' => [
        // Should be set to true in production
        'cache_enabled' => ENVIRONMENT === 'PROD',
        'cache_path' => $settings['temp'] . '/cache',
        'view_uri' => (ENVIRONMENT === 'PROD' ? 'https://recipera.be' : (ENVIRONMENT === 'TEST' ? 'http://recipera.prod' : 'http://localhost:80') )
    ],
];

$settings['session'] = [
    'name' => 'bricosk',
    'cache_expire' => 0,
    'cookie_secure' => ($settings['environment'] === 'PROD' ? true : false),
    'cookie_httponly' => true
];

// Logger settings
$settings['logger'] = [
    'name' => '_bsk',
    'path' => $settings['temp'] . '/logs/',
    'filename' => date('Y-m-d').'.log',
    'file_permission' => 0775,
];

return $settings;