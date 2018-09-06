<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_081848_add_company_logo_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%company}}', 'logo', Schema::TYPE_STRING . '(255) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%company}}', 'logo');
    }
}
