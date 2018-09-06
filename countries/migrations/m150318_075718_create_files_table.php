<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_075718_create_files_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%company_file}}', [
            'company_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'year' => Schema::TYPE_SMALLINT . '(4) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'file' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->addPrimaryKey('pk_company_file', '{{%company_file}}', ['company_id', 'year']);

        $this->createIndex('idx_company_file_company_id', '{{%company_file}}', 'company_id');

        $this->addForeignKey('fk_company_file_company', '{{%company_file}}', 'company_id', '{{%company}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%company_file}}');
    }
}
