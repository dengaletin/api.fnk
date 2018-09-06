<?php

use yii\db\Migration;
use yii\db\Schema;

class m170428_141208_add_raw_to_values extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%company_value}}', 'raw', 'LONGTEXT ' . ' DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%company_value}}', 'raw');
    }
}
