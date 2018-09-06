<?php

use yii\db\Migration;

class m160521_120014_create_registration_request_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%registration_request}}', [
            'id' => $this->primaryKey(),
            'confirm_code' => $this->string(8),
            'phone' => $this->string()->notNull(),
            'confirmed' => $this->boolean()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_registration_request_confirmed', '{{%registration_request}}', 'confirmed');
        $this->createIndex('idx_registration_request_confirm_code', '{{%registration_request}}', 'phone');
    }

    public function down()
    {
        $this->dropTable('{{%registration_request}}');
    }
}
