<?php

use yii\db\Schema;
use yii\db\Migration;

class m150402_134421_create_message_queue_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%message_queue}}', [
            'id' => Schema::TYPE_PK,
            'message_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'device_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . '(1) NOT NULL',
        ], $tableOptions);

        $this->createIndex('idx_message_queue_message_id', '{{%message_queue}}', 'message_id');
        $this->createIndex('idx_message_queue_device_id', '{{%message_queue}}', 'device_id');

        $this->addForeignKey('fk_message_queue_message', '{{%message_queue}}', 'message_id', '{{%message}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_message_queue_device', '{{%message_queue}}', 'device_id', '{{%device}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%message_queue}}');
    }
}
