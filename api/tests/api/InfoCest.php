<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Url;
use Yii;

class InfoCest extends ApiCestCase
{
    public function testAccessSend(ApiTester $I)
    {
        $I->sendGET('/api/send-info');
        $I->seeResponseCodeIs(401);
    }

    public function testAccessConfirm(ApiTester $I)
    {
        $I->sendGET('/api/confirm-info');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testVerbSend(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/send-info');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testVerbConfirm(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/confirm-info');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlankFirstName(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/send-info');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Empty first_name param',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlankLastName(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/send-info', [
            'first_name' => 'Name',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Empty last_name param',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlankPhone(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/send-info', [
            'first_name' => 'Name',
            'last_name' => 'Last',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Empty phone param',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testIncorrectPhone(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/send-info', [
            'first_name' => 'Name',
            'last_name' => 'Last',
            'phone' => '8 (56) 23',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Incorrect phone format',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlankEmail(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/send-info', [
            'first_name' => 'Name',
            'last_name' => 'Last',
            'phone' => '+7000000000',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Empty email param',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testCorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/send-info', [
            'first_name' => 'Name',
            'last_name' => 'Last',
            'phone' => '+7000000000',
            'email' => 'mail@test.com',
        ]);
        $I->seeResponseCodeIs(200);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testConfirmIncorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/confirm-info', [
            'code' => '111111',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Incorrect confirm code',
        ]);
    }

    public function testConfirmCorrect(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80Nw5T');
        $I->sendPOST('/api/confirm-info', [
            'code' => '123456',
        ]);
        $I->seeResponseCodeIs(200);
    }
}