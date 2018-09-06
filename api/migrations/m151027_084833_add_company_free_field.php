<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_084833_add_company_free_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%company}}', 'free', Schema::TYPE_SMALLINT . '(1) NOT NULL DEFAULT 0');

        $this->update('{{%company}}', ['free' => 1]);

        $this->createIndex('idx_company_free', '{{%company}}', 'free');
    }

    public function down()
    {
        $this->dropColumn('{{%company}}', 'free');
    }
}
