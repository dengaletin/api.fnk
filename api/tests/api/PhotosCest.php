<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use Yii;

class PhotosCest extends ApiCestCase
{
    private $data = [
        ['id' => 'test-11.png'],
        ['id' => 'test-12.png'],
        ['id' => 'test-13.png'],
        ['id' => 'test-14.png'],
        ['id' => 'test-21.png'],
    ];

    public function _before(\ApiTester $I)
    {
        parent::_before($I);
        $from = Yii::getAlias('@tests/_data/photos');
        $to = Yii::getAlias('@app/web/upload/photo');
        FileHelper::createDirectory($to);
        foreach ($this->data as $item) {
            $file = $item['id'];
            copy($from . DIRECTORY_SEPARATOR . $file, $to . DIRECTORY_SEPARATOR . $file);
        }
    }

    public function _after()
    {
        $path = Yii::getAlias('@app/web/upload/photo');
        foreach ($this->data as $item) {
            $file = $item['id'];
            unlink($path . DIRECTORY_SEPARATOR . $file);
            @unlink($path . DIRECTORY_SEPARATOR . 'photo-' . $file);
            @unlink($path . DIRECTORY_SEPARATOR . 'thumb-' . $file);
        }
    }

    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/photos');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlank(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/photos');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Missing required parameters: company_id',
            'status' => 400,
        ]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testByCompanyPurchase(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/photos?company_id=1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([$this->data[0], $this->data[1], $this->data[2]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testFileByCompanyPurchase(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/photo?id=' . $this->data[0]['id']);
        $I->seeResponseCodeIs(200);
        $I->seeHttpHeader('content-type', 'image/png');
    }
}