<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_083353_create_companies_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%company}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'property_form_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'ticker' => Schema::TYPE_STRING . ' NOT NULL',
            'mode_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'group_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'site' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);

        $this->createIndex('idx_company_property_form_id', '{{%company}}', 'property_form_id');
        $this->createIndex('idx_company_mode_id', '{{%company}}', 'mode_id');
        $this->createIndex('idx_company_group_id', '{{%company}}', 'group_id');

        $this->addForeignKey('fk_company_property_form', '{{%company}}', 'property_form_id', '{{%property_form}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_company_mode', '{{%company}}', 'mode_id', '{{%mode}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_company_group', '{{%company}}', 'group_id', '{{%group}}', 'id', 'CASCADE', 'RESTRICT');

        $this->createTable('{{%company_value}}', [
            'company_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'year' => Schema::TYPE_SMALLINT . '(4) NOT NULL',
            'report_type_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'currency_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'auditor' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'value_va' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_oa' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_ia' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_kir' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_dkiz' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_kkiz' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_v' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_fr' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_fd' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_frn' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_pdn' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_chpzp' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_aosina' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_chdspood' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_ebitda' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value_tebitda' => Schema::TYPE_DOUBLE . ' NULL DEFAULT NULL',
        ], $tableOptions);

        $this->addPrimaryKey('pk_company_value', '{{%company_value}}', ['company_id', 'year']);

        $this->createIndex('idx_company_value_company_id', '{{%company_value}}', 'company_id');
        $this->createIndex('idx_company_value_report_type_id', '{{%company_value}}', 'report_type_id');
        $this->createIndex('idx_company_value_currency_id', '{{%company_value}}', 'currency_id');

        $this->addForeignKey('fk_company_value_company', '{{%company_value}}', 'company_id', '{{%company}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_company_value_report_type', '{{%company_value}}', 'report_type_id', '{{%report_type}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_company_value_currency', '{{%company_value}}', 'currency_id', '{{%currency}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%company_value}}');

        $this->dropTable('{{%company}}');
    }
}
