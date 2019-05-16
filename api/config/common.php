<?php

use yii\helpers\ArrayHelper;

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
Yii::setAlias('@users_path', dirname(__DIR__) . '/web/upload/users');
Yii::setAlias('@apiv2', dirname(__DIR__) . '/modules/v2');

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'sourceLanguage'=>'en',
    'language'=>'ru',
    'name' => 'App',
    'components' => [
        'request' => [
            'enableCsrfValidation' => false
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => true,
            'rules' => [
                '<_c:[\w\-]+>' => '<_c>/index',
                '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
                '<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                [
                    'class' => 'yii\rest\UrlRule',
                    'prefix' => 'api',
                    'controller' => [
                        'v2/tokens',
                        'v2/users',
                        'v2/news',
                        'v2/companies',
                    ],
                ],

                // TokensController
                'POST api/v2/tokens/email' => 'v2/tokens/email',
                'POST api/v2/tokens/phone' => 'v2/tokens/phone',
                'POST api/v2/tokens/oauth' => 'v2/tokens/oauth',

                // UsersController
                'POST api/v2/users/<id:\d+>/email-confirm' => 'v2/users/email-confirm',
                'POST api/v2/users/<id:\d+>/phone-confirm' => 'v2/users/phone-confirm',
                'POST api/v2/users/<id:\d+>/email-update-confirm' => 'v2/users/email-update-confirm',
                'POST api/v2/users/<id:\d+>/phone-update-confirm' => 'v2/users/phone-update-confirm',
                'POST api/v2/users/<id:\d+>/avatar' => 'v2/users/avatar-create',
                'POST api/v2/users/<id:\d+>/avatar-update' => 'v2/users/avatar-update',
                'DELETE api/v2/users/<id:\d+>/avatar' => 'v2/users/avatar-delete',

                //Swagger
                'GET api/v2/swagger/docs' => 'v2/swagger/docs',
                'GET api/v2/swagger/json-schema' => 'v2/swagger/json-schema',
            ],
        ],
        'formatter' => [
            'nullDisplay' => '&nbsp;',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'smsSender' => [
            'class' => 'app\components\SmsSender',
        ],
        'apnPush' => [
            'class' => 'app\components\ApnPush',
            'certificateFile' => '@app/config/certificate.pem',
            'certificatePassPhrase' => null,
            'sandboxMode' => false,
        ],
        'fireSend' => [
            'class' => 'app\components\FireSend',
            'apiKey' => 'AAAAa8D9BU8:APA91bFs1oIu0TsRwXolTPie-RYFdcPcN1BwBAMtGpe2GMdBbF9Bbc-uV74fzKrko2ls1zH31CYFiH4CxNPyB0vskXfwc0r_Gv0shYFWl3iZljrxliPmDGrclZi6dEMUUqigwhkLdq1f',
            'batchSize' => '1000',
        ],
        'appStoreVerifier' => [
            'class' => 'app\components\AppStoreVerifier',
            'sandbox' => false,
            'active' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'log' => [
            'class' => 'yii\log\Dispatcher',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vk' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => '6983559',
                    'clientSecret' => 'L9zLQELDcy00oU8UrNLB',
                ],
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => 'google_client_id',
                    'clientSecret' => 'google_client_secret',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => 'facebook_client_id',
                    'clientSecret' => 'facebook_client_secret',
                ],
            ],
        ]
    ],
    'modules' => [
        'v2' => [
            'class' => 'app\modules\v2\Module',
        ],
    ],
    'params' => $params,
];
 