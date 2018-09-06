<?php

use yii\db\Schema;
use yii\db\Migration;

class m151217_080715_add_device_language_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%device}}', 'language', Schema::TYPE_STRING . '(2) NOT NULL');
        $this->update('{{%device}}', ['language' => 'EN']);

        $this->createIndex('idx_device_language', '{{%device}}', 'language');

        $this->addColumn('{{%message}}', 'language', Schema::TYPE_STRING . '(2) NOT NULL');
        $this->update('{{%message}}', ['language' => 'EN']);
    }

    public function down()
    {
        $this->dropColumn('{{%device}}', 'language');
        $this->dropColumn('{{%message}}', 'language');
    }
}
