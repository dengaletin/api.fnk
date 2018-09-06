<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Url;

class CurrenciesCest extends ApiCestCase
{
    private $data = [
        [
            'year' => 2011,
            'eur_avg' => 45.73,
            'eur_rep' => 44.26,
            'usd_avg' => 32.12,
            'usd_rep' => 33.17,
        ],
        [
            'year' => 2012,
            'eur_avg' => 46.73,
            'eur_rep' => 45.26,
            'usd_avg' => 34.12,
            'usd_rep' => 32.17,
        ],
    ];

    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/currencies');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAll(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/currencies');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($this->data);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testByYear(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/currencies?year=2011');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($this->data[0]);
    }
}