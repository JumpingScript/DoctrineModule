<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

ini_set('display_errors', true);
chdir(__DIR__);

$previousDir = '.';

while (!file_exists('config/application.config.php')) {
    $dir = dirname(getcwd());

    if ($previousDir === $dir) {
        throw new RuntimeException(
            'Unable to locate "config/application.config.php": ' .
            'is DoctrineModule in a subdir of your application skeleton?'
        );
    }

    $previousDir = $dir;
    chdir($dir);
}


if (is_readable('init_autoloader.php')) {
    include_once 'init_autoloader.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    include_once __DIR__ . '/../../../autoload.php';
} else {
    throw new RuntimeException('Error: vendor/autoload.php could not be found. Did you run php composer.phar install?');
}

if(file_exists('config/container.php')) {
    $serviceManager = include 'config/container.php';
} else {
    $appConfig = include 'config/application.config.php';
    if (file_exists('config/development.config.php')) {
        $appConfig = ArrayUtils::merge($appConfig, include 'config/development.config.php');
    }
    $application = Application::init($appConfig);
    $serviceManager = $application->getServiceManager();
}

/* @var $cli \Symfony\Component\Console\Application */
$cli = $serviceManager->get('doctrine.cli');
exit($cli->run());
