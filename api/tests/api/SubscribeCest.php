<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Json;
use yii\helpers\Url;

class SubscribeCest extends ApiCestCase
{
    public function testAddControl(ApiTester $I)
    {
        $I->sendGET('/api/subscribe');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAddVerb(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/subscribe');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAddBlank(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/subscribe');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Invalid device_token param',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAddExisted(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/subscribe', ['device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0faee']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAddIncorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/subscribe', ['device_token' => 'f1be1085']);
        $I->seeResponseCodeIs(500);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Internal Server Error',
            'message' => Json::encode(['device_token' => ['Device Token should contain 64 characters.']]),
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAddCorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/subscribe', ['device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0fae0']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testRemoveVerb(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/unsubscribe');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    public function testRemoveNotExisted(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80007');
        $I->sendPOST('/api/unsubscribe');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Not Found',
            'message' => 'Subscribe is not found',
        ]);
    }

    public function testRemoveCorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80005');
        $I->sendPOST('/api/subscribe', ['device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe9cb5253cbc8fd9672631bcf0fa31']);
        $I->seeResponseCodeIs(200);

        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80005');
        $I->sendPOST('/api/unsubscribe');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');
    }
}