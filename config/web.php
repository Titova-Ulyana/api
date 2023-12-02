<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name'=> 'sweetshop',
    'language'=> 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'sweetshop',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            //'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) 
                {
                    $response = $event->sender;
                    if ($response->data !== null && $response->statusCode==401) 
                        {
                            $response->data = ['error'=>['code'=>401, 'message'=>'Unauthorized',
                            'errors'=>['phone'=>'login or password incorrect']]];
                            header('Access-Control-Allow-Origin: *');
                            header('Content-Type: application/json');
                        }
                },
            ],
           
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                /*['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'product'],// и так далее все табл.   
                ['class' => 'yii\rest\UrlRule', 'controller' => 'orders'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'category'],*/

                'POST register' => 'user/create',
                'POST login' => 'user/login',
                'GET account' => 'user/view',

                'GET catalog' => 'product/index',
                'GET card_product/<id>' => 'product/view',

                'POST create_order' => 'orders/create',
                'DELETE del_order/<id>' => 'orders/delete',
                //admin
                    'PATCH change_status/<id>' => 'orders/change',

                'POST create_product' => 'product/create',
                'DELETE del_product/<id>' => 'product/delete',
                    'PATCH change_product/<id>' => 'product/change',
                
                'POST create_category' => 'category/create',
                'DELETE del_category/<id>' => 'category/delete',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1','*'],
    ];
}

return $config;
