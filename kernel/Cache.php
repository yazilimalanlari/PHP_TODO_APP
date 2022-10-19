<?php

namespace Kernel;

class Cache {
    private static function create(): void {
        if (!file_exists(CACHE_PATH)) {
            mkdir(CACHE_PATH);
        }
    }

    public static function clean(): void {
        if (file_exists(CACHE_PATH)) {
            shell_exec(sprintf('rm -rf %s', CACHE_PATH));
        }
    }

    public static function createFile(string $path): void {
        if (!file_exists($path)) touch($path);
    }

    public static function write(string $name, array $data): void {
        self::create();
        self::createFile($path = CACHE_PATH . "/$name.php");
        
        $array = var_export($data, true);
        $output = sprintf('<?php%s return %s;', str_repeat(PHP_EOL, 2), $array);
        file_put_contents($path, $output);
    }

    public static function read(string $name, mixed $default = null): mixed {
        $path = CACHE_PATH . "/$name.php";
        if (file_exists($path)) return require_once $path;
        return $default;
    }
}