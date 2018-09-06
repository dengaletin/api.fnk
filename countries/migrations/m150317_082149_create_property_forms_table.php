<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_082149_create_property_forms_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%property_form}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%property_form}}');
    }
}
