<?php
/**
 * Application configuration shared by all test types
 */
return [
    'language' => 'en-US',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/fixtures',
            'templatePath' => '@tests/templates',
            'namespace' => 'tests\fixtures',
        ],
    ],
    'components' => [
        'db' => [
            'dsn' => '',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'smsSender' => [
            'appId' => '111',
            'active' => false,
        ],
        'appStoreVerifier' => [
            'sandbox' => true,
            'active' => false,
        ],
    ],
];
