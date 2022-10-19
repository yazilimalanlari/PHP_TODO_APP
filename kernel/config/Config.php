<?php

namespace Kernel;

use Kernel\Router;

class Config {
    const API_TYPE_DYNAMIC = 'DYNAMIC';
    const API_TYPE_DEFAULT = 'DEFAULT';

    public static function init(bool $useRoutersXML = false, string $appApiType = self::API_TYPE_DYNAMIC): void {
        if (getenv('MODE') === 'development') {
            
        }

        switch (getenv('MODE')) {
            case 'development': self::preload([ 'useRoutersXML' => $useRoutersXML ]); break;
            case 'production': self::load(); break;
        }
        
        if (!defined('BUILD')) {
            Router::run();
        }
    }

    public static function preload(array $options): void {
        Router::process($options);
    }

    public static function load(): void {
        Router::init();
    }

    public static function build() {
        Router::build();
    }
}