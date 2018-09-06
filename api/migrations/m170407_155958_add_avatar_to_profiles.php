<?php

use yii\db\Migration;
use yii\db\Schema;

class m170407_155958_add_avatar_to_profiles extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%profile}}', 'avatar', Schema::TYPE_STRING . '(255) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%profile}}', 'avatar');
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
