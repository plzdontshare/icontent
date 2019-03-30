<?php

declare(strict_types=1);

set_time_limit(0);
ini_set('max_execution_time', '0');

define('ROOT_PATH', realpath(dirname(__FILE__)));

require ROOT_PATH . '/vendor/autoload.php';

$console = new \Symfony\Component\Console\Application;
$console->add(new \IContent\Console\DownloadContent);

$console->run();