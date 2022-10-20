<?php

namespace Kernel;

use Error;
use Exception;
use \Kernel\Config\Cache;
use Kernel\Web\Request;

class Router {
    private static array $routes = [];
    private const ROUTERS_PATH = BASE_PATH . '/routers';
    private const ROUTES_ATTRIBUTES = ['prefix', 'controller'];
    private const ROUTE_ATTRIBUTES = ['path', 'method', 'cmethod'];
    private static $MIME_TYPES = [
        'css' => 'text/css',
        'js' => 'text/javascript',
        'ico' => 'image/vnd.microsoft.icon'
    ];

    public static function init(): void {
        self::$routes = Cache::read('routes', []);
    }
    
    public static function process(array $options): void {
        if ($options['useRoutersXML']) {
            self::loadRoutersForXML();
        } else {
            require BASE_PATH . '/routers/routers.php';
        }
    }

    private static function loadRoutersForXML(): void {
        $xml = simplexml_load_file(self::ROUTERS_PATH . '/routers.xml');

        foreach ($xml->route as $item) {
            $attrs = Utils::getXMLAttributes($item->attributes(), self::ROUTE_ATTRIBUTES);
            [$controllerName, $methodName] = explode('::', $attrs['cmethod']);
            $attrs['cmethod'] = $methodName;
            self::routeAdd($controllerName, self::routePathToRegexPattern($attrs));
        }

        foreach ($xml->routes as $item) {
            extract(Utils::getXMLAttributes($item->attributes(), self::ROUTES_ATTRIBUTES)); // controller, prefix
            
            foreach ($item->route as $routeItem) {
                $route = Utils::getXMLAttributes($routeItem->attributes(), self::ROUTE_ATTRIBUTES);
                $route['path'] = self::routePathToRegexPattern(($prefix ?? '') . $route['path']);
                self::routeAdd($controller, $route);
            }
        }
    }
    
    public static function routeAdd(string $cName, array $route): void {
        self::$routes[$cName] = array_key_exists($cName, self::$routes) ? [...self::$routes[$cName], $route] : [$route];
    }

    public static function routePathToRegexPattern(array|string $routeOrPath): array|string {
        $path = is_string($routeOrPath) ? $routeOrPath : $routeOrPath['path'];
        $path = preg_replace_callback('/:([a-zA-Z]+[a-zA-Z0-9]+)(\(.*\))?/', function($match) use (&$params) {
            return isset($match[2]) ? str_replace('(', "(?<$match[1]>", $match[2]) : "(?<$match[1]>[a-zA-Z0-9]+)";
        }, $path);
        $path = '&^' . rtrim($path, '/') . '/?$&';
        
        if (is_string($routeOrPath)) return $path;
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

        foreach (Config::$staticFiles as $fileUrl => $item) {
            if ($fileUrl === $url) {
                header('Content-Type: ' . self::getMimeType($item['fileName']));
                die(file_get_contents($item['path']));
            }
        }

        foreach (self::$routes as $cName => $routes) {
            foreach ($routes as $route) {
                if ($_SERVER['REQUEST_METHOD'] !== $route['method']) continue;
                preg_match($route['path'], $url, $result);
                $params = array_filter($result, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                if ($result) {
                    self::matchedURL($cName, $route['cmethod'], $params);
                    return;
                }
            }
        }
    }

    private static function matchedURL(string $controllerName, string $methodName, array $params): void {
        $class = "\\Controller\\$controllerName";
        $controller = new $class;

        if (method_exists($controller, $methodName)) {
            try {
                $request = new Request([
                    'params' => $params,
                    'input' => self::getInputData(),
                    'query' => $_GET
                ]);
                $result = call_user_func([$controller, $methodName], $request);
                die($result);
            } catch (Exception $e) {
                die('There is a problem!');
            }
        } else {
            throw new Error("Method not found! Method: $controllerName::$methodName");
        }
    }

    private static function getMimeType(string $fileName): string {
        return self::$MIME_TYPES[pathinfo($fileName, PATHINFO_EXTENSION)];
    }

    private static function getInputData(): array {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST': return $_POST;
            case 'PUT':
                $input = fopen('php://input', 'r');
                $result = '';
                while ($data = fread($input, 1024)) $result .= $data;
                fclose($input);
                
                $putData = [];
                foreach (explode('&', $result) as $row) {
                    [$name, $value] = explode('=', $row, 2);
                    $putData[$name] = urldecode(trim($value));
                }
                return $putData;
        }
        return [];
    }
}