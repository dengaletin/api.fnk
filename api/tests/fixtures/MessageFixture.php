<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class MessageFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Message';
    public $dataFile = '@tests/_data/fixtures/message.php';
}
