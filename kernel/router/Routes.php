<?php

namespace Kernel\Router;

interface IRoutes {
    public static function controller(string $cName, ?string $prefix = null, ?callable $cb = null): void;
}

class Routes implements IRoutes {
    /**
     * Last controller name
     *
     * @var string|null
     */
    public static ?string $controllerName = null;
    
    /**
     * Last route prefix
     *
     * @var string|null
     */
    public static ?string $prefix = null;

    /**
     * Route controller wrapper
     * 
     * @param string $controllerName
     * @param string $prefix
     * @return void
     */
    public static function controller(string $cName, ?string $prefix = null, ?callable $cb = null): void {
        self::$controllerName = $cName;
        self::$prefix = $prefix;
        if ($cb != null) $cb();
        self::$controllerName = null;
        self::$prefix = null;
    }
}