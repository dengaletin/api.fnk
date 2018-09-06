<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class CompanyValueFixture extends ActiveFixture
{
    public $modelClass = 'app\models\CompanyValue';
    public $dataFile = '@tests/_data/fixtures/company-value.php';
}
