<?php

use yii\db\Schema;
use yii\db\Migration;

class m150421_123939_add_file_unique_key extends Migration
{
    public function up()
    {
        $this->createIndex('idx_company_file_file', '{{%company_file}}', 'file', true);
    }

    public function down()
    {
        $this->dropIndex('idx_company_file_file', '{{%company_file}}');
    }
}
