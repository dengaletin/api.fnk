<?php

use yii\db\Migration;
use yii\db\Schema;

class m170407_131514_add_nickname_to_profile extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%profile}}', 'nickname', Schema::TYPE_STRING . '(255)');

        $this->createIndex('idx_profile_nickname', '{{%profile}}', 'nickname');
    }

    public function down()
    {
        $this->dropColumn('{{%profile}}', 'nickname');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
