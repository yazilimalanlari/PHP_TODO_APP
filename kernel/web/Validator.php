<?php

namespace Kernel\Web;

abstract class Validator {
    /**
     * @return array
     */   
    abstract protected function format(): array;

    /**
     * @param string $validationName
     * @param array $data
     * @return array
     */
    protected function validate(string $validationName, array $data): array {
        $validation = $this->format()[$validationName];
        $responseData = [];
        $errors = [];

        foreach ($validation as $field => $item) {
            $properties = $this->parseValidation($item);
            foreach ($properties as $property => $value) {
                $result = $this->validateForPropertyAndValue($property, $value, $data[$field] ?? null);
                if ($result !== true) {
                    $errors[$field] = $result;
                    break;
                }
            }
            $responseData[$field] = $data[$field] ?? null;
        }

        if (!empty($errors)) return ['errors' => $errors];
        return [ 'data' => $responseData ];
    }

    /**
     * @param string $validation
     * @return array
     */
    private function parseValidation(string $validation): array {
        $result = [];
        foreach (explode('&', $validation) as $item) {
            $parse = explode(':', $item);
            $result[$parse[0]] = $parse[1] ?? true;
        }
        return $result;
    }

    /**
     * @param string $property
     * @param string $value
     * @param string|null $dataValue
     * @return boolean|string
     */
    private function validateForPropertyAndValue(string $property, string $value, ?string $dataValue): bool|string {
        switch ($property) {
            case 'required': 
                if ($dataValue == null) return "This field is required!";
                break;
            case 'type':
                if ($value === 'email') {
                    if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                        return 'Invalid e-mail address!'; 
                } else if ($value === 'int') {
                    if (!is_numeric($dataValue)) return 'Value is not numeric!';
                }
                break;
            case 'minlength': 
                if ($value != null && strlen($dataValue) < $value) 
                    return "Minimum length can be $value characters!";
                break;
            case 'maxlength': 
                if ($value != null && strlen($dataValue) > $value) 
                    return "Maximum length can be $value characters!";
                break;
        }
        return true;
    }
}