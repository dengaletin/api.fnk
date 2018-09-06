<?php

use yii\db\Schema;
use yii\db\Migration;

class m150519_082533_remove_currencies_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%company_value}}', 'currency', Schema::TYPE_STRING . '(32) NULL DEFAULT NULL');

        $this->dropForeignKey('fk_company_value_currency', '{{%company_value}}');

        $this->dropIndex('idx_company_value_currency_id', '{{%company_value}}');

        $this->dropColumn('{{%company_value}}', 'currency_id');

        $this->dropTable('{{%currency}}');
    }

    public function down()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->dropColumn('{{%company_value}}', 'currency');

        $this->createTable('{{%currency}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);

        $this->addColumn('{{%company_value}}', 'currency_id', Schema::TYPE_INTEGER . ' NULL DEFAULT NULL');

        $this->createIndex('idx_company_value_currency_id', '{{%company_value}}', 'currency_id');

        $this->addForeignKey('fk_company_value_currency', '{{%company_value}}', 'currency_id', '{{%currency}}', 'id', 'CASCADE', 'RESTRICT');
    }
}
