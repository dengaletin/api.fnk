<?php

use yii\db\Schema;
use yii\db\Migration;

class m150402_073809_create_year_currency_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%year_currency}}', [
            'year' => Schema::TYPE_INTEGER . '(4) NOT NULL',
            'eur_avg' => Schema::TYPE_DOUBLE . ' NULL DEFAULT NULL',
            'eur_rep' => Schema::TYPE_DOUBLE . ' NULL DEFAULT NULL',
            'usd_avg' => Schema::TYPE_DOUBLE . ' NULL DEFAULT NULL',
            'usd_rep' => Schema::TYPE_DOUBLE . ' NULL DEFAULT NULL',
        ], $tableOptions);

        $this->addPrimaryKey('pk_year_currency', '{{%year_currency}}', 'year');
    }

    public function down()
    {
        $this->dropTable('{{%year_currency}}');
    }
}
