<?php

spl_autoload_register(function ($className) {
    if (str_starts_with($className, 'Controller')) {
        $className = implode('\\', array_replace(explode('\\', $className), ['\\Src\\Controllers']));
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