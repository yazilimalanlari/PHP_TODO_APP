<?php

namespace Kernel;

use Kernel\Router;

class Config {
    public static $staticFiles = [];

    public static function init(bool $useRoutersXML = false): void {
        date_default_timezone_set('UTC');
        self::loadEnvironmentVariables(__CWD__ . '/.env');
        ini_set('session.gc_maxlifetime', 3600 * 24);

        switch (getenv('MODE')) {
            case 'development': self::preload([ 'useRoutersXML' => $useRoutersXML ]); break;
            case 'production': self::load(); break;
        }
        
        if (!defined('BUILD')) {
            require_once __CWD__ . '/kernel/utils/mvc.php';
            self::public();
            Router::run();
        }
    }

    public static function preload(array $options): void {
        Router::process($options);
    }

    public static function load(): void {
        error_reporting(0);
        ini_set('display_errors', 0);
        Router::init();
    }

    public static function build() {
        Router::build();
        $models = array_map(fn($fileName) => current(explode('.', $fileName)), array_diff(scandir(BASE_PATH . '/models'), ['.', '..']));
        
        // Database Models
        foreach ($models as $modelName) {
            $class = "\Model\\$modelName";
            $model = new $class;
            $model->setup();
        }
    }

    private static function loadEnvironmentVariables(string $path): void {
        $content = file_get_contents($path);
        $rows = array_filter(explode(PHP_EOL, $content));
        
        foreach ($rows as $row) {
            if (str_starts_with(trim($row), '#')) continue;
            [$name, $value] = explode('=', $row, 2);
            $value = trim(current(explode('#', $value, 2)));
            putenv("$name=$value");
        }
    }

    private static function public(?string $path = null) {
        $cb = function (string $name, string $path, string $type) {
            if ($type === 'folder') {
                self::public($path);
            } else {
                self::$staticFiles[str_replace(PUBLIC_PATH, '', $path)] = [
                    'fileName' => $name,
                    'path' => $path
                ];
            }
        };
        Utils::getDirectoryItemsCallback($path ?? PUBLIC_PATH, $cb);
    }
}