<?php

namespace Kernel\Router;

use Kernel\Router;

interface IRoute {
    public static function get(string $path, string $cmethod): void;
    public static function post(string $path, string $cmethod): void;
    public static function put(string $path, string $cmethod): void;
    public static function delete(string $path, string $cmethod): void;
}

class Route implements IRoute {
    /**
     * @param string $path
     * @param string $method
     * @param string $cmethod
     * @return void
     */
    private static function routeFormattedAdd(string $path, string $method, string $cmethod) {
        if (Routes::$controllerName != null) {
            $controllerName = Routes::$controllerName;
            $methodName = $cmethod;
        } else {
            [$controllerName, $methodName] = explode('::', $cmethod);
        }
        
        $route = [
            'path' => Router::routePathToRegexPattern((Routes::$prefix ?? '') . $path),
            'method' => $method,
            'cmethod' => $methodName,
            'auth' => Routes::$auth
        ];
        
        Router::routeAdd($controllerName, $route);
    }
    
    /**
     * @param string $path
     * @param string $cmethod
     * @return void
     */
    public static function get(string $path, string $cmethod): void {
        self::routeFormattedAdd($path, 'GET', $cmethod);
    }

    /**
     * @param string $path
     * @param string $cmethod
     * @return void
     */
    public static function post(string $path, string $cmethod): void {
        self::routeFormattedAdd($path, 'POST', $cmethod);
    }

    /**
     * @param string $path
     * @param string $cmethod
     * @return void
     */
    public static function put(string $path, string $cmethod): void {
        self::routeFormattedAdd($path, 'PUT', $cmethod);
    }

    /**
     * @param string $path
     * @param string $cmethod
     * @return void
     */
    public static function delete(string $path, string $cmethod): void {
        self::routeFormattedAdd($path, 'DELETE', $cmethod);
    }
}