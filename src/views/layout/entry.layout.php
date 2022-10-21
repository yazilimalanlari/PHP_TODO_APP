<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title><?=getenv('APP_TITLE')?> | <?=$title?></title>
        <link rel="stylesheet" href="/css/theme.css">
        <link rel="stylesheet" href="/css/entry.css">
        <?=apiInfoForHTML()?>
    </head>
    <body>
        <main class="container">
            <div class="entry primary-content">
                <?=$CONTENT ?? null?>
            </div>
        </main>
        
        <script type="module" src="/js/api.js"></script>
        <script type="module" src="/js/entry.js"></script>
    </body>
</html>