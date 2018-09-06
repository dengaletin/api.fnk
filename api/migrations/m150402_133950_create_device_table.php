<?php

use yii\db\Schema;
use yii\db\Migration;

class m150402_133950_create_device_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%device}}', [
            'id' => Schema::TYPE_PK,
            'token' => Schema::TYPE_STRING . '(40) NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%device}}');
    }
}
