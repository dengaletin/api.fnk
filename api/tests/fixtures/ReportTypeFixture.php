<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class ReportTypeFixture extends ActiveFixture
{
    public $modelClass = 'app\models\ReportType';
    public $dataFile = '@tests/_data/fixtures/report-type.php';
}
