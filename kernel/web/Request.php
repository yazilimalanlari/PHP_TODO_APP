<?php

namespace Kernel\Web;

class Request {
    public Auth $auth;
    
    public function __construct(private array $data) {
        $this->auth = new Auth();
    }
    
    /**
     * @param string $name
     * @return string|null
     */
    public function param(string $name): ?string {
        return $this->getValue($this->data['params'], $name);
    }

    /**
     * Default input
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
    public function inputFilter(string $name): ?string {
        $value = $this->getInputValue($name);
        if ($value == null) return null;
        return htmlspecialchars($value);
    }

    /**
     * Default query
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
    public function queryFilter(string $name): ?string {
        $value = $this->getValue($this->data['query'], $name);
        if ($value == null) return null;
        return htmlspecialchars($value);
    }

    /**
     * Default all params
     * @return array
     */
    public function getParams(): array {
        return $this->data['params'];
    }

    /**
     * Default all inputs
     * @return array
     */
    public function getInputs(): array {
        return $this->data['input'];
    }

    /**
     * Default all queries
     * @return array
     */
    public function getQueries(): array {
        return $this->data['query'];
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