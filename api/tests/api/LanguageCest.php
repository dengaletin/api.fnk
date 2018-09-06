<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Json;
use yii\helpers\Url;

class LanguageCest extends ApiCestCase
{
    public function testSetControl(ApiTester $I)
    {
        $I->sendGET('/api/set-language');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testSetVerb(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/set-language');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testSetBlank(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/set-language');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Invalid language param',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testSetIncorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/set-language', ['language' => 'KT']);
        $I->seeResponseCodeIs(500);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Internal Server Error',
            'message' => Json::encode(['language' => ['Language is incorrect.']]),
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testSetCorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/set-language', ['language' => 'RU']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');
    }
}