<?php

use yii\db\Schema;
use yii\db\Migration;

class m170224_170426_registration_request_add_data_field extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%registration_request}}', 'profile_data', 'LONGTEXT NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%registration_request}}', 'profile_data');
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
