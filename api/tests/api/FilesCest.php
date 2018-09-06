<?php

namespace tests\api;

use \ApiTester;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use Yii;

class FilesCest extends ApiCestCase
{
    private $data = [
        [
            'year' => 2011,
            'lang' => 'ru',
            'name' => 'test-file-1-2011-ru.pdf',
            'id' => 'test-11',
        ],
        [
            'year' => 2011,
            'lang' => 'en',
            'name' => 'test-file-1-2011-en.pdf',
            'id' => 'test-12',
        ],
        [
            'year' => 2012,
            'lang' => 'en',
            'name' => 'test-file-1-2012-en.pdf',
            'id' => 'test-13',
        ],
        [
            'year' => 2013,
            'lang' => 'ru',
            'name' => 'test-file-1-2013-ru.pdf',
            'id' => 'test-14',
        ],
        [
            'year' => 2013,
            'lang' => 'ru',
            'name' => 'test-file-2-2013-ru.pdf',
            'id' => 'test-21',
        ],
    ];

    public function _before(\ApiTester $I)
    {
        parent::_before($I);
        $from = Yii::getAlias('@tests/_data/files');
        $to = Yii::getAlias('@app/web/upload');
        FileHelper::createDirectory($to);
        foreach ($this->data as $item) {
            $file = $item['id'] . '.pdf';
            copy($from . DIRECTORY_SEPARATOR . $file, $to . DIRECTORY_SEPARATOR . $file);
        }
    }

    public function _after()
    {
        $path = Yii::getAlias('@app/web/upload');
        foreach ($this->data as $item) {
            $file = $item['id'] . '.pdf';
            unlink($path . DIRECTORY_SEPARATOR . $file);
        }
    }

    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/files');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testBlank(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/files');
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
        $I->sendGET('/api/files?company_id=2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContainsJson([$this->data[4]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testByCompanyPurchase(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/files?company_id=1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([$this->data[0], $this->data[1], $this->data[2]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testByCompanyAndYear(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/files?company_id=1&year=2012');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($this->data[2]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testByCompanyAndLang(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/files?company_id=1&lang=en');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([$this->data[1], $this->data[2]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testByCompanyAndYearAndLang(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/files?company_id=1&year=2011&lang=en');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([$this->data[1]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testMissingEngByCompany(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/files?company_id=2&lang=en');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([$this->data[4]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testMissingEngByCompanyAndYear(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/files?company_id=2&year=2013&lang=en');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([$this->data[4]]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testFileByCompanyPurchase(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/file?id=' . $this->data[0]['id']);
        $I->seeResponseCodeIs(200);
        $I->seeHttpHeader('content-type', 'application/pdf');
    }
}