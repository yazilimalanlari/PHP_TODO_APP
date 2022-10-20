<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title><?=getenv('APP_TITLE') ?? 'App'?></title>
        <link rel="stylesheet" href="/css/theme.css">
    </head>
    <body>
        <?=$CONTENT ?? null?>
    </body>
</html>