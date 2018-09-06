<?php

use yii\db\Schema;
use yii\db\Migration;

class m150519_084233_rename_year_currency_table extends Migration
{
    public function up()
    {
        $this->dropPrimaryKey('pk_year_currency', '{{%year_currency}}', 'year');

        $this->renameTable('{{%year_currency}}', '{{%currency}}');

        $this->addPrimaryKey('pk_currency', '{{%currency}}', 'year');
    }

    public function down()
    {
        $this->dropPrimaryKey('pk_currency', '{{%currency}}', 'year');

        $this->renameTable('{{%currency}}', '{{%year_currency}}');

        $this->addPrimaryKey('pk_year_currency', '{{%year_currency}}', 'year');
    }
}
