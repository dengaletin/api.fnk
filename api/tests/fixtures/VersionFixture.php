<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class VersionFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Version';
    public $dataFile = '@tests/_data/fixtures/version.php';
}
