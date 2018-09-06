<?php

namespace app\commands;

use app\models\Profile;
use app\models\Purchase;
use yii\console\Controller;
use Yii;

class CronController extends Controller
{
    public function actionExpirePurchase()
    {
        $apnPush = $this->getApnPush();

        $query = Purchase::find()
            ->expiredSoon(Yii::$app->params['purchaseExpireNotifyTimeout'])
            ->notNotified()
            ->with('device');

        foreach ($query->each() as $purchase) {
            $purchase->updateAttributes(['notified' => true]);
            $apnPush->send($purchase->device->device_token, 'Your purchased period will have finished soon.');
            $this->stdout($purchase->device->device_token . PHP_EOL);
        }
    }

    public function actionExpireProfile()
    {
        $apnPush = $this->getApnPush();

        $query = Profile::find()
            ->expiredSoon(Yii::$app->params['profileExpireNotifyTimeout'])
            ->notNotified()
            ->with('device');

        foreach ($query->each() as $profile) {
            $profile->updateAttributes(['notified' => true]);
            $apnPush->send($profile->device->device_token, 'Your profile period will have finished soon.');
            $this->stdout($profile->device->device_token . PHP_EOL);
        }
    }

    /**
     * @return \app\components\ApnPush
     */
    private function getApnPush()
    {
        return Yii::$app->get('apnPush');
    }
}
