<?php

use yii\db\Schema;
use yii\db\Migration;

class m151217_095131_create_company_photos_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%company_photo}}', [
            'id' => Schema::TYPE_PK,
            'company_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'file' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex('idx_company_photo_company_id', '{{%company_photo}}', 'company_id');

        $this->addForeignKey('fk_company_photo_company', '{{%company_photo}}', 'company_id', '{{%company}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%company_photo}}');
    }
}
