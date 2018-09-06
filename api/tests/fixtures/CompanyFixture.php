<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class CompanyFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Company';
    public $dataFile = '@tests/_data/fixtures/company.php';
}
