<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_141223_add_device_purchase_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%device}}', 'receipt', Schema::TYPE_TEXT);
        $this->addColumn('{{%device}}', 'purchase', Schema::TYPE_SMALLINT . '(1) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('{{%device}}', 'receipt');
        $this->dropColumn('{{%device}}', 'purchase');
    }
}
