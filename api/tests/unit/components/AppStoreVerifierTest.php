<?php

namespace tests\unit\components;

use app\components\AppStoreVerifier;
use Codeception\Test\Unit;
use Yii;

class AppStoreVerifierTest extends Unit
{
    public function _before()
    {
        if (!@fsockopen('buy.itunes.apple.com', 443)) {
            $this->markTestSkipped();
        }
    }

    public function testSimpleReceipt()
    {
        $verifier = new AppStoreVerifier([
            'sandbox' => true,
            'active' => true,
        ]);

        $receipt = file_get_contents(Yii::getAlias('@tests/_data/receipts/01'));

        expect('receipt is valid', $verifier->validateReceipt($receipt, 'com.iFinik.iFinik', 'com.iFinik.inAppIFinikPro'))->true();
    }

    public function testSecretReceipt()
    {
        $verifier = new AppStoreVerifier([
            'sandbox' => true,
            'active' => true,
            'secret' => '2c39094b7ea4438f8195cee3c492f8f8',
        ]);

        $receipt = file_get_contents(Yii::getAlias('@tests/_data/receipts/05'));

        expect('receipt is valid', $verifier->validateReceipt($receipt, 'com.iFinik.iFinik', 'com.iFinik.inAppIFinikPro'))->true();
    }

    public function testSandboxReceiptInProduction()
    {
        $verifier = new AppStoreVerifier([
            'sandbox' => false,
            'active' => true,
            'secret' => '2c39094b7ea4438f8195cee3c492f8f8',
        ]);

        $receipt = file_get_contents(Yii::getAlias('@tests/_data/receipts/05'));

        $this->expectException('app\components\AppStoreException');
        $this->expectExceptionMessage('Sandbox receipt on production environment usage.');

        $verifier->validateReceipt($receipt, 'com.iFinik.iFinik', 'com.iFinik.inAppIFinikPro');
    }

    public function testProductionReceiptInSandbox()
    {
        $verifier = new AppStoreVerifier([
            'sandbox' => true,
            'active' => true,
            'secret' => '2c39094b7ea4438f8195cee3c492f8f8',
        ]);

        $receipt = file_get_contents(Yii::getAlias('@tests/_data/receipts/06'));

        $this->expectException('app\components\AppStoreException');
        $this->expectExceptionMessage('Production receipt on sandbox environment usage.');

        $verifier->validateReceipt($receipt, 'com.iFinik.iFinik', 'com.iFinik.inAppIFinikPro');
    }
}