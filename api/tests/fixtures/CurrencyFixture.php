<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class CurrencyFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Currency';
    public $dataFile = '@tests/_data/fixtures/currency.php';
}
