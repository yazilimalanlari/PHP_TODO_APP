<?php

use Kernel\Utils;

/**
 * @param string $viewName
 * @param array $vars
 * @param boolean $layout
 * @return string
 */
function view(string $viewName, array $vars = [], string|bool $layout = false): string {
    $path = BASE_PATH . "/views/$viewName.view.php";
    $content = Utils::callFileBufferContent($path, $vars);

    if ($layout !== false) {
        $layout = $layout === true ? 'common' : $layout;
        $layout = BASE_PATH . "/views/layout/$layout.layout.php";
        $content = Utils::callFileBufferContent($layout, [
            ...$vars,
            'CONTENT' => $content
        ]);
    }

    return $content;
}

function response(mixed $input): string {
    switch (gettype($input)) {
        case 'array': 
            header('Content-Type: application/json');
            http_response_code(array_key_exists('errors', $input) ? 406 : 200);
            return json_encode($input);
        case 'string': return $input;
    }
}

function apiInfoForHTML() {
    $apiBaseUrl = getenv('APP_URL');
    $apiPath = '/api';

    $js = <<<JS
    const API_BASE_URL = "$apiBaseUrl";
    const API_PATH = "$apiPath";
    JS;

    return "<script>$js</script>";
}