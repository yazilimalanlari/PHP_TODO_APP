<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title><?=getenv('APP_TITLE')?></title>
        <link rel="stylesheet" href="/css/theme.css">
        <link rel="stylesheet" href="/css/common.css">
        <?=apiInfoForHTML()?>
    </head>
    <body>
        <?=$CONTENT ?? null?>

        <script type="module" src="/js/api.js"></script>
        <script type="module" src="/js/common.js"></script>
    </body>
</html>