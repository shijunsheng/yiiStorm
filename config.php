<?php

$rules = require __DIR__ . '/config/rules.php';
$db    = require __DIR__ . '/config/db.php';
$redis = require __DIR__ . '/config/redis.php';
$mongo = require __DIR__ . '/config/mongo.php';

return [
    'id' => 'app',
    'basePath' => __DIR__,
    'controllerNamespace' => 'app\controllers',
    'components' => [

        'db' => $db,

        'redis' => $redis,

        'mongodb' => $mongo,

        'amqp' => function () {
            $amqp = require __DIR__ . '/config/amqp.php';
            return new \PhpAmqpLib\Connection\AMQPStreamConnection($amqp['host'],$amqp['port'],$amqp['user'],$amqp['password']);
        },

        'es' => function () {
            $hosts = require __DIR__ . '/config/es.php';
            $client = new \Elasticsearch\ClientBuilder;
            return $client::create()->setHosts($hosts)->build();
        },

        'err' => function () {
            return  require __DIR__ . '/config/err.php';
        },

        'common' => function () {
            return  require __DIR__ . '/config/common.php';
        },

        'urlManager' => [

            # 开启URL美化
            'enablePrettyUrl' => true,

            # 隐藏index.php
            'showScriptName' => true,

            # 路由严格匹配模式
            'enableStrictParsing' => true,

            // rules 路由
            'rules' => $rules
        ],

        'request' => [

            'cookieValidationKey' => 'T--c803ao7WOKplhnrlu9PG20JCN-MYP',

            # 关闭跨站请求验证
            'enableCsrfValidation' => false,
        ]
    ]
];
