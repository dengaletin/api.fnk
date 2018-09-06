<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_133234_create_version_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%version}}', [
            'id' => Schema::TYPE_PK,
            'date' => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%version}}');
    }
}
