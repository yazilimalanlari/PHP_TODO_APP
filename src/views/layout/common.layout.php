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

        <header>
            <nav>
                <a href="/logout">
                    <?=file_get_contents(PUBLIC_PATH . '/images/icons/logout.svg')?>
                </a>
            </nav>
        </header>
        
        <?=$CONTENT ?? null?>

        <script src="/js/utils.js"></script>
        <script type="module" src="/js/api.js"></script>
        <script type="module" src="/js/common.js"></script>
    </body>
</html>