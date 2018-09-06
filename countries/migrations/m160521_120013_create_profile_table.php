<?php

use yii\db\Migration;

class m160521_120013_create_profile_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'phone_confirm_code' => $this->string(8),
            'email' => $this->string()->notNull(),
            'confirm' => $this->boolean()->notNull()->defaultValue(0),
            'expired_at' => $this->integer(),
            'notified' => $this->boolean()->notNull()->defaultValue(0)
        ], $tableOptions);

        $this->createIndex('idx_profile_device_id', '{{%profile}}', 'device_id', true);
        $this->createIndex('idx_profile_confirm', '{{%profile}}', 'confirm');
        $this->createIndex('idx_profile_expired_at', '{{%profile}}', 'expired_at');
        $this->createIndex('idx_profile_notified', '{{%profile}}', 'notified');

        $this->addForeignKey('fk_profile_device', '{{%profile}}', 'device_id', '{{%device}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
    }
}
