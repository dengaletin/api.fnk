<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_083209_create_reporttypes_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%report_type}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%report_type}}');
    }
}
