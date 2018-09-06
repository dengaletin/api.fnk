<?php

namespace tests\unit\models;

use app\models\Message;
use Codeception\Test\Unit;
use tests\fixtures\DeviceFixture;
use tests\fixtures\MessageFixture;
use tests\fixtures\MessageQueueFixture;
use tests\fixtures\PurchaseFixture;
use yii\helpers\ArrayHelper;

class MessageTest extends Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    public function _before()
    {
        $this->tester->haveFixtures([
            'devices' => ['class' => DeviceFixture::className()],
            'messages' => ['class' => MessageFixture::className()],
            'messageQueues' => ['class' => MessageQueueFixture::className()],
            'purchases' => ['class' => PurchaseFixture::className()],
        ]);
    }

    public function testQueueAllEN()
    {
        $model = new Message([
            'message' => 'Test Message',
            'target' => Message::TARGET_ALL,
            'language' => 'EN',
        ]);

        expect('message saved', $model->save())->true();
        expect('queues are correct', ArrayHelper::getColumn($model->queues, 'device_id'))->equals([1, 2, 3]);
    }

    public function testQueuePurchaseEN()
    {
        $model = new Message([
            'message' => 'Test Message',
            'target' => Message::TARGET_PURCHASE,
            'language' => 'EN',
        ]);

        expect('message saved', $model->save())->true();
        expect('queues are correct', ArrayHelper::getColumn($model->queues, 'device_id'))->equals([2]);
    }

    public function testQueueFreeEN()
    {
        $model = new Message([
            'message' => 'Test Message',
            'target' => Message::TARGET_FREE,
            'language' => 'EN',
        ]);

        expect('message saved', $model->save())->true();
        expect('queues are correct', ArrayHelper::getColumn($model->queues, 'device_id'))->equals([1, 3]);
    }

    public function testQueuePurchaseRU()
    {
        $model = new Message([
            'message' => 'Test Message',
            'target' => Message::TARGET_PURCHASE,
            'language' => 'RU',
        ]);

        expect('message saved', $model->save())->true();
        expect('queues are correct', ArrayHelper::getColumn($model->queues, 'device_id'))->equals([]);
    }

    public function testQueueFreeRU()
    {
        $model = new Message([
            'message' => 'Test Message',
            'target' => Message::TARGET_FREE,
            'language' => 'RU',
        ]);

        expect('message saved', $model->save())->true();
        expect('queues are correct', ArrayHelper::getColumn($model->queues, 'device_id'))->equals([0 => 4, 1 => 6]);
    }
}