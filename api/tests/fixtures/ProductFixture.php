<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Fixture
 */
class ProductFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Product';
    public $dataFile = '@tests/_data/fixtures/product.php';
}
