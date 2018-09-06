<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_125658_add_message_target_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%message}}', 'target', Schema::TYPE_STRING. '(8) NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%message}}', 'target');
    }
}
