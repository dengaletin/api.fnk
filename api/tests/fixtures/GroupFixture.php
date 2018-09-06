<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class GroupFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Group';
    public $dataFile = '@tests/_data/fixtures/group.php';
}
