<?php
if (defined('BASE_DIRS')) return;
const BASE_DIRS = [
    'Controller' => '\\Src\\Controllers',
    'Model' => '\\Src\\Models',
    'Router' => '\\Src\\Routers',
    'Service' => '\\Src\\Services',
    'View' => '\\Src\\Views'
];

spl_autoload_register(function ($className) {
    if (($i = array_search(current(explode('\\', $className)), array_keys(BASE_DIRS))) !== false) {
        $className = implode('\\', array_replace(explode('\\', $className), [array_values(BASE_DIRS)[$i]]));
    }
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $dirname = dirname($path);
    $basename = basename($path);
    $first = join('/', [__CWD__, strtolower($dirname), $basename]) . '.php';
    
    if (file_exists($first)) {
        require $first;
    } else if (file_exists($last = join('/', [__CWD__, strtolower($path), $basename]) . '.php')) {
        require $last;
    } else {
        throw new Error("File not found! filePath: $first and $last");
    }
});