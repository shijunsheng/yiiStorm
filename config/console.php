<?php

$conf = require dirname(__DIR__) . '/config.php';

$config =
[
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'components' => $conf['components']
];

return $config;
