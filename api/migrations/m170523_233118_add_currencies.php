<?php

use yii\db\Migration;

class m170523_233118_add_currencies extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%currency}}', 'eurusd_rep', 'double ' . ' DEFAULT NULL');
        $this->addColumn('{{%currency}}', 'eurusd_avg', 'double ' . ' DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%currency}}', 'eurusd_rep');
        $this->dropColumn('{{%currency}}', 'eurusd_avg');
    }
}
