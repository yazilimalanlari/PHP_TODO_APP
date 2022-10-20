<?php

namespace Kernel;

use SimpleXMLElement;
use Error;

class Utils {
    public static function getXMLAttributes(SimpleXMLElement $attributes, array $allowedAttrs, bool $isError = false): array {
        return array_merge(...array_map(function($attrName) use ($attributes, $isError) {
            if (isset($attributes->$attrName))
                return [ $attrName => (string) $attributes->$attrName ];
            else if ($isError)
                throw new Error("Missing attr! $attrName");
            return [];
        }, $allowedAttrs));
    }

    public static function getDirectoryItemsCallback(string $path, callable $cb): void {
        $dir = opendir($path);
        while (($name = readdir($dir)) !== false && !in_array($name, ['.', '..'])) {
            $fullPath = join('/', [$path, $name]);
            $cb($name, $fullPath, is_dir($fullPath) ? 'folder' : 'file');
        }
        closedir($dir);
    }

    public static function callFileBufferContent(string $path, array $vars = []): string {
        ob_start();
        extract($vars);
        require $path;
        return ob_get_clean();
    }
}