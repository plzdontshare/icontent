<?php

declare(strict_types=1);

set_time_limit(0);
ini_set('max_execution_time', '0');

set_error_handler('exceptions_error_handler');

define('ROOT_PATH', realpath(dirname(__FILE__)));

require ROOT_PATH . '/vendor/autoload.php';

$console = new \Symfony\Component\Console\Application;
$console->add(new \IContent\Console\DownloadContent);

$console->run();


function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
}