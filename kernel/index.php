<?php
require __DIR__ . '/config/constants.php';
require __DIR__ . '/config/preconfig.php';

use \Kernel\Config;

Config::init(
    appApiType: Config::API_TYPE_DEFAULT,
    useRoutersXML: false
);