<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_082840_create_groups_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%group}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%group}}');
    }
}
