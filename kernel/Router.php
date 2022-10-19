<?php

namespace Kernel;

class Router {
    private static array $routes = [];
    private const ROUTERS_PATH = BASE_PATH . '/routers';
    private const ROUTES_ATTRIBUTES = ['prefix', 'controller'];
    private const ROUTE_ATTRIBUTES = ['path', 'method', 'cmethod'];

    public static function init(): void {
        self::$routes = Cache::read('routes', []);
    }
    
    public static function process(array $options): void {
        if ($options['useRoutersXML']) {
            self::loadRoutersForXML();
        }
    }

    private static function loadRoutersForXML(): void {
        $xml = simplexml_load_file(self::ROUTERS_PATH . '/routers.xml');
        
        $routeAdd = fn(string $cName, array $route) => self::$routes[$cName] = array_key_exists($cName, self::$routes) ? [...self::$routes[$cName], $route] : [$route];;
        
        foreach ($xml->route as $item) {
            $attrs = Utils::getXMLAttributes($item->attributes(), self::ROUTE_ATTRIBUTES);
            [$controllerName, $methodName] = explode('::', $attrs['cmethod']);
            $attrs['cmethod'] = $methodName;
            $routeAdd($controllerName, self::routePathToRegexPattern($attrs));
        }

        foreach ($xml->routes as $item) {
            extract(Utils::getXMLAttributes($item->attributes(), self::ROUTES_ATTRIBUTES));
            
            foreach ($item->route as $routeItem) {
                $route = [
                    ...Utils::getXMLAttributes($routeItem->attributes(), self::ROUTE_ATTRIBUTES),
                    'prefix' => $prefix
                ];
                $routeAdd($controller, self::routePathToRegexPattern($route));
            }
        }
    }

    private static function routePathToRegexPattern(array|string $routeOrPath): array|string {
        $path = $routeOrPath['path'] ?: $routeOrPath;
        $path = preg_replace_callback('/:([a-zA-Z]+)(\(.*\))?/', function($match) use (&$params) {
            return isset($match[2]) ? str_replace('(', "(?<$match[1]>", $match[2]) : "(?<$match[1]>[a-zA-Z0-9]+)";
        }, $path);
        $path = "&^$path/?$&";
        
        if (gettype($routeOrPath) === 'string') return $path;
        return [
            ...$routeOrPath,
            'path' => $path
        ];
    }

    public static function build(): void {
        Cache::write('routes', self::$routes);
    }

    public static function run(): void {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach (self::$routes as $cName => $routes) {
            foreach ($routes as $route) {
                if ($_SERVER['REQUEST_METHOD'] !== $route['method']) continue;
                preg_match($route['path'], $url, $result);
                $params = array_filter($result, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                self::matchedURL($cName, $route['cmethod'], $params);
                if ($result) return;
            }
        }
    }

    private static function matchedURL(string $controllerName, string $methodName, array $params): void {
        $class = "\\Controller\\$controllerName";
        $controller = new $class;

        if (method_exists($controller, $methodName)) {
            $result = $controller->$methodName();
            echo $result;
        }
    }
}