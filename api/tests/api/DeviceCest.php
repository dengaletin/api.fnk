<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Json;
use yii\helpers\Url;

class DeviceCest extends ApiCestCase
{
    public function testRemoveControl(ApiTester $I)
    {
        $I->sendGET('/api/remove-device');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testRemoveVerb(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/remove-device');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    public function testRemoveNotExisted(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80000');
        $I->sendPOST('/api/remove-device');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Not Found',
            'message' => 'Device is not found',
        ]);
    }

    public function testRemoveCorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80001');
        $I->sendPOST('/api/subscribe', ['device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0fa22']);
        $I->seeResponseCodeIs(200);

        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80001');
        $I->sendPOST('/api/remove-device');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');
    }
}