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
    http_response_code(200);
    switch (gettype($input)) {
        case 'array': return json_encode($input);
        case 'string': return $input;
    }
}