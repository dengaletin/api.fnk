<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_134642_change_device_token_field extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%device}}', 'token', Schema::TYPE_STRING . '(64)');
    }

    public function down()
    {
        $this->alterColumn('{{%device}}', 'token', Schema::TYPE_STRING . '(64) NOT NULL');
    }
}
