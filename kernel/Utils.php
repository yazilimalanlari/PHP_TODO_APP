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
}