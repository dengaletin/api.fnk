<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class ProfileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Profile';
    public $dataFile = '@tests/_data/fixtures/profile.php';
}
