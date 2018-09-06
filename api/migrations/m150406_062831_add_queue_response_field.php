<?php

use yii\db\Schema;
use yii\db\Migration;

class m150406_062831_add_queue_response_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%message_queue}}', 'response', Schema::TYPE_TEXT . ' NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%message_queue}}', 'response');
    }
}
