<?php

use yii\db\Schema;
use yii\db\Migration;

class m151005_103147_remove_property_forms_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%company}}', 'property_form', Schema::TYPE_STRING . ' NULL DEFAULT NULL');

        $this->execute('UPDATE {{%company}} c SET property_form = (
            SELECT form.name FROM {{%property_form}} form WHERE form.id = c.property_form_id
        )');

        $this->dropForeignKey('fk_company_property_form', '{{%company}}');

        $this->dropColumn('{{%company}}', 'property_form_id');

        $this->dropTable('{{%property_form}}');
    }

    public function down()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%company}}', 'property_form_id', Schema::TYPE_INTEGER . ' NULL DEFAULT NULL');

        $this->createIndex('idx_company_property_form_id', '{{%company}}', 'property_form_id');

        $this->createTable('{{%property_form}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk_company_property_form', '{{%company}}', 'property_form_id', '{{%property_form}}', 'id', 'CASCADE', 'RESTRICT');

        $this->execute('
            INSERT INTO {{%property_form}} (
                name
            )
            SELECT
                c.property_form
            FROM {{%company}} c
            GROUP BY c.property_form
            ORDER BY c.property_form ASC
        ');

        $this->execute('UPDATE {{%company}} c SET property_form_id = (
            SELECT MIN(form.id) FROM {{%property_form}} form WHERE form.name = c.property_form GROUP BY c.property_form
        )');

        $this->dropColumn('{{%company}}', 'property_form');
    }
}
