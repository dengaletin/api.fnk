<?php

namespace tests\api;

use tests\fixtures\CompanyFileFixture;
use tests\fixtures\CompanyFixture;
use tests\fixtures\CompanyPhotoFixture;
use tests\fixtures\CompanyValueFixture;
use tests\fixtures\CurrencyFixture;
use tests\fixtures\DeviceFixture;
use tests\fixtures\GroupFixture;
use tests\fixtures\MessageFixture;
use tests\fixtures\MessageQueueFixture;
use tests\fixtures\ModeFixture;
use tests\fixtures\ProductFixture;
use tests\fixtures\ProfileFixture;
use tests\fixtures\PurchaseFixture;
use tests\fixtures\ReportTypeFixture;
use tests\fixtures\VersionFixture;

class ApiCestCase
{
    protected $productId = 'com.iFinik.inAppIFinikPro';
    protected $accessToken;

    function _before(\ApiTester $I)
    {
        $I->haveFixtures([
            'versions' => [
                'class' => VersionFixture::className(),
                'dataFile' => '@tests/_data/fixtures/version.php',
            ],
            'groups' => [
                'class' => GroupFixture::className(),
                'dataFile' => '@tests/_data/fixtures/group.php',
            ],
            'modes' => [
                'class' => ModeFixture::className(),
                'dataFile' => '@tests/_data/fixtures/mode.php',
            ],
            'companies' => [
                'class' => CompanyFixture::className(),
                'dataFile' => '@tests/_data/fixtures/company.php',
            ],
            'reportTypes' => [
                'class' => ReportTypeFixture::className(),
                'dataFile' => '@tests/_data/fixtures/report-type.php',
            ],
            'companyValues' => [
                'class' => CompanyValueFixture::className(),
                'dataFile' => '@tests/_data/fixtures/company-value.php',
            ],
            'companyFiles' => [
                'class' => CompanyFileFixture::className(),
                'dataFile' => '@tests/_data/fixtures/company-file.php',
            ],
            'companyPhotos' => [
                'class' => CompanyPhotoFixture::className(),
                'dataFile' => '@tests/_data/fixtures/company-photo.php',
            ],
            'currencies' => [
                'class' => CurrencyFixture::className(),
                'dataFile' => '@tests/_data/fixtures/currency.php',
            ],
            'devices' => [
                'class' => DeviceFixture::className(),
                'dataFile' => '@tests/_data/fixtures/device.php',
            ],
            'profiles' => [
                'class' => ProfileFixture::className(),
                'dataFile' => '@tests/_data/fixtures/profile.php',
            ],
            'products' => [
                'class' => ProductFixture::className(),
                'dataFile' => '@tests/_data/fixtures/product.php',
            ],
            'purchases' => [
                'class' => PurchaseFixture::className(),
                'dataFile' => '@tests/_data/fixtures/purchase.php',
            ],
            'messages' => [
                'class' => MessageFixture::className(),
                'dataFile' => '@tests/_data/fixtures/message.php',
            ],
            'messageQueues' => [
                'class' => MessageQueueFixture::className(),
                'dataFile' => '@tests/_data/fixtures/message-queue.php',
            ],
        ]);
    }

    protected function loginAsFree(\ApiTester $I)
    {
        $this->accessToken = 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80Nv0I';
    }

    protected function loginAsPurchase(\ApiTester $I)
    {
        $this->accessToken = 'eEe4Zu0g2g5QDjjBqnL3q3E1kL80Nv0R';
    }

    protected function logout(\ApiTester $I)
    {
        $this->accessToken = null;
    }
} 