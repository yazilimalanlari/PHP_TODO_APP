<?php

namespace Kernel\Web;

class Request {
    public function __construct(private array $data) {}
    
    /**
     * @param string $name
     * @return string|null
     */
    public function param(string $name): ?string {
        return $this->getValue($this->data['params'], $name);
    }

    /**
     * Original input
     * @param string $name
     * @return string|null
     */
    public function input(string $name): ?string {
        return $this->getInputValue($name);
    }

    /**
     * Input with htmlspecialchars
     * @param string $name
     * @return string|null
     */
    public function inputClean(string $name): ?string {
        $value = $this->getInputValue($name);
        if ($value == null) return null;
        return htmlspecialchars($value);
    }

    /**
     * Original query
     * @param string $name
     * @return string|null
     */
    public function query(string $name): ?string {
        return $this->getValue($this->data['query'], $name);
    }
    
    /**
     * Query with htmlspecialchars
     * @param string $name
     * @return string|null
     */
    public function queryClean(string $name): ?string {
        $value = $this->getValue($this->data['query'], $name);
        if ($value == null) return null;
        return htmlspecialchars($value);
    }

    private function getInputValue(string $name): ?string {
        return $this->getValue($this->data['input'], $name);
    }

    private function getValue(array $data, string $name): ?string {
        if (array_key_exists($name, $data)) {
            return $data[$name];
        }
        return null;
    }
}