<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        // 缓存组件
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'keyPrefix' => 'myapp', // 避免不同应用的缓存的 key 出现重复
        ],
        /**
         * 使用数据库作为缓存引擎的案例
         */
//        'cache' => [
//            'class' => 'yii\caching\DbCache',
//            'db' => 'mydb',
//            'cacheTable' => 'my_cache',
//        ],
        // 配置授权组件
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
];
