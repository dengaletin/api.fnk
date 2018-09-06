<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_092051_rename_company_property_form_field extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%company}}', 'property_form', 'name_full');
        $this->renameColumn('{{%company}}', 'property_form_eng', 'name_full_eng');
    }

    public function down()
    {
        $this->renameColumn('{{%company}}', 'name_full', 'property_form');
        $this->renameColumn('{{%company}}', 'name_full_eng', 'property_form_eng');
    }
}
