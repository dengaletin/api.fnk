<?php

use yii\db\Schema;
use yii\db\Migration;

class m150402_153205_add_queue_status_index extends Migration
{
    public function up()
    {
        $this->createIndex('idx_message_queue_status', '{{%message_queue}}', 'status');
    }

    public function down()
    {
        $this->dropIndex('idx_message_queue_status', '{{%message_queue}}');
    }
}
