<?php

use yii\db\Schema;
use yii\db\Migration;

class m151106_115008_change_company_description_fields extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%company}}', 'description_eng', Schema::TYPE_TEXT . ' NULL');
    }

    public function down()
    {
        $this->addColumn('{{%company}}', 'description_eng', Schema::TYPE_STRING . ' NULL');
    }
}
