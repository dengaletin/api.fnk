<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\Url;
use Yii;

class PurchaseCest extends ApiCestCase
{
    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/purchase');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAddVerb(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/purchase');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Method Not Allowed']);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlank(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/purchase');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Invalid receipt param',
        ]);
    }

    public function testExistingDevice(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $token = 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80Nv0T');
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'purchase' => false,
        ]);

        $I->haveHttpHeader('X-User-Token', $token);
        $I->sendPOST('/api/purchase', [
            'receipt' => file_get_contents(Yii::getAlias('@tests/_data/receipts/01')),
            'pid' => $this->productId,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');

        $I->haveHttpHeader('X-User-Token', $token);
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'purchase' => true,
        ]);
    }

    public function testNewDevice(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $token = 'eEe4Zu0g2g5QDjj000L3q3E1kL80N000');
        $I->sendPOST('/api/purchase', [
            'receipt' => file_get_contents(Yii::getAlias('@tests/_data/receipts/02')),
            'pid' => $this->productId,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');

        $I->haveHttpHeader('X-User-Token', $token);
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'purchase' => true,
        ]);
    }

    public function testExistedReceipt(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80Nv0T');
        $I->sendPOST('/api/purchase', [
            'receipt' => file_get_contents(Yii::getAlias('@tests/_data/receipts/03')),
            'pid' => $this->productId,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');

        $I->sendPOST('/api/purchase', [
            'receipt' => file_get_contents(Yii::getAlias('@tests/_data/receipts/03')),
            'pid' => $this->productId,
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Purchase already exists',
        ]);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testNotExistingProduct(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendPOST('/api/purchase', [
            'receipt' => file_get_contents(Yii::getAlias('@tests/_data/receipts/03')),
            'pid' => 'com.iFinik.inAppNotExists',
        ]);
        $I->seeResponseCodeIs(404);
        $I->seeResponseContainsJson([
            'name' => 'Not Found',
            'message' => 'Product not found',
        ]);
    }

    public function testPro30Days(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $token = 'eEe4Zu0g2g5QDjj000L3q3E1kL80N005');
        $I->sendPOST('/api/subscribe', ['device_token' => 'f1be1085bcedf90304f7f04da7e0f81aecbe6cb5253cbc8fd9672631bcf0fae8']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');

        $I->haveHttpHeader('X-User-Token', $token);
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'purchase' => false,
        ]);

        $I->haveHttpHeader('X-User-Token', $token);
        $I->sendPOST('/api/purchase', [
            'receipt' => file_get_contents(Yii::getAlias('@tests/_data/receipts/04')),
            'pid' => 'com.iFinik.inAppIFinikPro30Days',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('"ok"');

        $I->haveHttpHeader('X-User-Token', $token);
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'purchase' => true,
        ]);
    }
}