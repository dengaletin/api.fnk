<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_141036_add_device_access_token_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%device}}', 'access_token', Schema::TYPE_STRING . '(255)');

        $this->createIndex('idx_device_access_token', '{{%device}}', 'access_token');
    }

    public function down()
    {
        $this->dropColumn('{{%device}}', 'access_token');
    }
}
