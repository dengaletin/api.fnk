<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class CompanyFileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\CompanyFile';
    public $dataFile = '@tests/_data/fixtures/company-file.php';
}
