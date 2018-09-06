<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class ModeFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Mode';
    public $dataFile = '@tests/_data/fixtures/mode.php';
}
