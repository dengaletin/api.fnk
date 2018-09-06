<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class PurchaseFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Purchase';
    public $dataFile = '@tests/_data/fixtures/purchase.php';
}
