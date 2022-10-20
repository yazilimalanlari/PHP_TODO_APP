#!/usr/bin/php
<?php
require __DIR__ . '/kernel/config/constants.php';
require __DIR__ . '/kernel/config/preconfig.php';

use Kernel\Config\Cache;
use Kernel\Config;

array_shift($argv);

define('SCRIPT_FILE', __DIR__ . '/kernel/index.php');

function build(): void {
    putenv('MODE=development');
    define('BUILD', true);
    require SCRIPT_FILE;
    Config::build();
}

function dev(): void {
    putenv('MODE=development');
    $command = sprintf('php -S localhost:3000 %s', SCRIPT_FILE);
    shell_exec($command);
}

function start(): void {
    putenv('MODE=production');
    $command = sprintf('php -S localhost:3000 %s', SCRIPT_FILE);
    shell_exec($command);
}

switch (current($argv)) {
    case 'build': build(); break;
    case 'dev': dev(); break;
    case 'start': start(); break;
    case 'cache':
        if (next($argv) === 'clean') {
            Cache::clean();
        }
        break;
}