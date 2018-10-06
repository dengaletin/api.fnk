<?php

use app\components\parser\ParsersController;

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
Yii::$classMap['app\components\parser\ParsersController'] = __DIR__ . '/../components/parser/ParsersController.php';

return [
    'id' => 'app-console',
    'bootstrap' => ['gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'aliases' => [
        '@webroot' => dirname(__DIR__) . '/web'
    ],
    'controllerMap' => [
        'parsers' => [
            'class' => ParsersController::className()
        ],
    ],
];
