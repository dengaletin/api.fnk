<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class DeviceFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Device';
    public $dataFile = '@tests/_data/fixtures/device.php';
}
