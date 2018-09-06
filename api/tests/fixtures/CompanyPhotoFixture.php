<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class CompanyPhotoFixture extends ActiveFixture
{
    public $modelClass = 'app\models\CompanyPhoto';
    public $dataFile = '@tests/_data/fixtures/company-photo.php';
}
