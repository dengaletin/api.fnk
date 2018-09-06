<?php

namespace app\components;

use alxmsl\AppStore\Client;
use alxmsl\AppStore\Exception\ProductionReceiptOnSandboxException;
use alxmsl\AppStore\Exception\SandboxReceiptOnProductionException;
use alxmsl\AppStore\Response\iOS6\Status;
use yii\base\Component;
use yii\base\InvalidConfigException;

class AppStoreVerifier extends Component
{
    public $sandbox = true;
    public $secret = '';
    public $active = true;

    /**
     * @param $receipt
     * @param $bid
     * @param $productId
     * @throws InvalidConfigException
     * @return bool
     */
    public function validateReceipt($receipt, $bid, $productId)
    {
        if (empty($productId) || empty($bid)) {
            throw new InvalidConfigException('Product_ID and BID must be set.');
        }
        if ($this->active) {
            try {
                $AppStore = new Client();
                $AppStore->setSandbox($this->sandbox);
                $AppStore->setPassword($this->secret);
                /** @var \alxmsl\AppStore\Response\iOS7\ResponsePayload $response */
                $response = $AppStore->verifyReceipt($this->clearReceipt($receipt));
                if ($response->getStatus() == Status::STATUS_OK) {
                    if ($response->getAppReceipt()->getBundleId() == $bid) {
                        foreach ($response->getAppReceipt()->getInAppPurchaseReceipts() as $receipt) {
                            if ($receipt->getProductId() == $productId) {
                                return true;
                            }
                        }
                    }
                }
                return false;
            } catch (ProductionReceiptOnSandboxException $e) {
                throw new AppStoreException('Production receipt on sandbox environment usage.');
            } catch (SandboxReceiptOnProductionException $e) {
                throw new AppStoreException('Sandbox receipt on production environment usage.');
            }
        } else {
            return true;
        }
    }

    private function clearReceipt($receipt)
    {
        return trim(preg_replace('#\s+#s', '', $receipt));
    }
}