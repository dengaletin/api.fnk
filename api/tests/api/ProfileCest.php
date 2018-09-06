<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Url;
use Yii;

class ProfileCest extends ApiCestCase
{
    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testFree(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0faee',
            'purchase' => false,
            'language' => 'EN',
        ]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testPurchase(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0fae1',
            'purchase' => true,
            'language' => 'EN',
        ]);
    }

    public function testNew(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80NvAA');
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'device_token' => null,
            'purchase' => false,
            'language' => null,
        ]);
    }
}