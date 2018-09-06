<?php

use yii\helpers\ArrayHelper;

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

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
            'rules' => [
                '<_c:[\w\-]+>' => '<_c>/index',
                '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
                '<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
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
    ],
    'params' => $params,
];
 