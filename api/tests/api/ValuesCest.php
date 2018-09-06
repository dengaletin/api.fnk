<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Url;

class ValuesCest extends ApiCestCase
{
    private $data = [
        [
            'company_id' => 1,
            'year' => 2010,
            'auditor' => 'PWC',
            'auditor_eng' => 'PWC',
            'value_va' => '197147.000',
            'value_oa' => '58554.000',
            'value_ia' => '255701.000',
            'value_kir' => '193769.000',
            'value_dkiz' => '11770.000',
            'value_kkiz' => '4976.000',
            'value_v' => '145298.000',
            'value_fr' => '-169.000',
            'value_fd' => '2428.000',
            'value_frn' => '2259.000',
            'value_pdn' => '10998.000',
            'value_chpzp' => '8668.000',
            'value_aosina' => '-12214.000',
            'value_chdspood' => '22710.000',
            'value_ebitda' => '20953.000',
            'value_tebitda' => 0.79921729585262,
            'currency' => 'Млн руб.',
            'currency_eng' => 'Mln rub.',
            'report_type' => 'МСФО',
            'report_type_eng' => 'MSFO',
        ],
        [
            'company_id' => 1,
            'year' => 2011,
        ],
        [
            'company_id' => 2,
            'year' => 2011,
        ],
    ];

    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/values');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlank(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/values');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Missing required parameters: company_id',
            'status' => 400,
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testByCompanyFree(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/values?company_id=2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContainsJson([['company_id' => 2]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testByCompanyPurchase(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/values?company_id=1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([$this->data[0], $this->data[1]]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testByCompanyAndYear(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/values?company_id=1&year=2011');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($this->data[1]);
    }
}