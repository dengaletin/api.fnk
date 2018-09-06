<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_163909_rename_device_token_field extends Migration
{
    public function up()
    {
        $this->dropIndex('idx_device_token', '{{%device}}');

        $this->renameColumn('{{%device}}', 'token', 'device_token');

        $this->createIndex('idx_device_device_token', '{{%device}}', 'device_token');
    }

    public function down()
    {
        $this->dropIndex('idx_device_device_token', '{{%device}}');

        $this->renameColumn('{{%device}}', 'device_device', 'token');

        $this->createIndex('idx_device_token', '{{%device}}', 'token');
    }
}
