<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class MessageQueueFixture extends ActiveFixture
{
    public $modelClass = 'app\models\MessageQueue';
    public $dataFile = '@tests/_data/fixtures/message-queue.php';
}
