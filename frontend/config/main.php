<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    // 默认首页
    'defaultRoute' => 'post/index',
    // 语言
    'language' => 'zh-CN',

    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        /*
        // url 管理组件配置
        'urlManager' => [
            // 开启 url 美化，可以去掉 index.php?r= 这一部分
            'enablePrettyUrl' => true,
            // 是否在 url 中显示脚本文件，也就是 index.php
            'showScriptName' => false,
            // 配置 url 伪静态后缀
            'suffix' => '.html',

            // 美化规则
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/detail',
                'posts' => 'post/index',
            ],
        ],
        */

    ],
    'params' => $params,
];
