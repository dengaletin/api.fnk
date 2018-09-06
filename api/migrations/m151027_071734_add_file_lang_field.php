<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_071734_add_file_lang_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%company_file}}', 'lang', Schema::TYPE_STRING . '(8) NOT NULL');

        $this->update('{{%company_file}}', ['lang' => 'ru']);

        $this->createIndex('idx_company_file_lang', '{{%company_file}}', 'lang');

        $this->dropPrimaryKey('pk_company_file', '{{%company_file}}');
        $this->addPrimaryKey('pk_company_file', '{{%company_file}}', ['company_id', 'year', 'lang']);
    }

    public function down()
    {
        $this->dropPrimaryKey('pk_company_file', '{{%company_file}}');

        $this->dropColumn('{{%company_file}}', 'lang');

        $this->addPrimaryKey('pk_company_file', '{{%company_file}}', ['company_id', 'year']);
    }
}
