<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Url;

class VersionCest extends ApiCestCase
{
    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/version');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testVersion(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/version');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('2');
    }
}