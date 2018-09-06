<?php

use yii\db\Schema;
use yii\db\Migration;

class m150403_162903_change_device_token_field extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%device}}', 'token', Schema::TYPE_STRING . '(64)');
        $this->createIndex('idx_device_token', '{{%device}}', 'token');
    }

    public function down()
    {
        $this->dropIndex('idx_device_token', '{{%device}}');
        $this->alterColumn('{{%device}}', 'token', Schema::TYPE_STRING . '(40)');
    }
}
