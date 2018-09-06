<?php

use yii\db\Migration;

class m160521_111257_add_purchase_notified_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%purchase}}', 'notified', $this->boolean()->notNull()->defaultValue(0));
        $this->update('{{%purchase}}', ['notified' => true]);

        $this->createIndex('idx_purchase_notified', '{{%purchase}}', 'notified');
    }

    public function down()
    {
        $this->dropColumn('{{%purchase}}', 'notified');
    }
}
