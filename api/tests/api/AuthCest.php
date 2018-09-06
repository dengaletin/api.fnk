<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Url;

class AuthCest extends ApiCestCase
{
    public function testVerb(\ApiTester $I)
    {
        $I->sendGET('/api/auth');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    public function testAuthNew(\ApiTester $I)
    {
        $I->sendPOST('/api/auth');
        $I->seeResponseCodeIs(200);
        $I->haveHttpHeader('X-User-Token', $I->grabResponse());
    }

    public function testAuthExisted(\ApiTester $I)
    {
        $I->sendPOST('/api/auth', ['device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0fa12']);
        $I->seeResponseCodeIs(200);
        $I->haveHttpHeader('X-User-Token', $I->grabResponse());
    }

    public function testAuthNotExisted(\ApiTester $I)
    {
        $I->sendPOST('/api/auth', ['device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0f000']);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Not Found',
            'message' => 'Device is not found',
        ]);
    }
}