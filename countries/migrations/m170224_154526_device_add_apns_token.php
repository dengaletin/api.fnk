<?php

use yii\db\Schema;
use yii\db\Migration;

class m170224_154526_device_add_apns_token extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%device}}', 'apns_token', Schema::TYPE_STRING . '(1024)');
        $this->addColumn('{{%device}}', 'firebase_token', Schema::TYPE_STRING . '(1024)');

        $this->createIndex('idx_device_apns_token', '{{%device}}', 'apns_token');
        $this->createIndex('idx_device_firebase_token', '{{%device}}', 'firebase_token');

        $this->execute('
            UPDATE {{%device}} SET
                apns_token = device_token
        ');
    }

    public function down()
    {
        $this->dropColumn('{{%device}}', 'apns_token');
        $this->dropColumn('{{%device}}', 'firebase_token');

        return false;
    }
}
