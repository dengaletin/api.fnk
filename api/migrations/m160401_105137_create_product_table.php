<?php

use yii\db\Migration;

class m160401_105137_create_product_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'bid' => $this->string()->notNull(),
            'pid' => $this->string()->notNull(),
            'days' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_product_pid', '{{%product}}', 'pid');
        $this->createIndex('idx_product_bid', '{{%product}}', 'bid');

        $this->insert('{{%product}}', [
            'id' => 1,
            'name' => 'Full Access',
            'bid' => 'com.iFinik.iFinik',
            'pid' => 'com.iFinik.inAppIFinikPro',
            'days' => null,
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%product}}');
    }
}
