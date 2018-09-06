<?php

use yii\db\Schema;
use yii\db\Migration;

class m151008_091630_add_eng_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%company}}', 'name_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%company}}', 'property_form_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%company}}', 'name_for_list', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%company}}', 'name_for_list_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%company}}', 'ticker_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%company}}', 'description_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');

        $this->addColumn('{{%mode}}', 'name_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%group}}', 'name_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');

        $this->addColumn('{{%report_type}}', 'name_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');

        $this->addColumn('{{%company_value}}', 'auditor_eng', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%company_value}}', 'currency_eng', Schema::TYPE_STRING . '(32) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%company}}', 'name_eng');
        $this->dropColumn('{{%company}}', 'name_for_list');
        $this->dropColumn('{{%company}}', 'name_for_list_eng');
        $this->dropColumn('{{%company}}', 'property_form_eng');
        $this->dropColumn('{{%company}}', 'ticker_eng');
        $this->dropColumn('{{%company}}', 'description_eng');

        $this->dropColumn('{{%mode}}', 'name_eng');
        $this->dropColumn('{{%group}}', 'name_eng');

        $this->dropColumn('{{%report_type}}', 'name_eng');

        $this->dropColumn('{{%company_value}}', 'auditor_eng');
        $this->dropColumn('{{%company_value}}', 'currency_eng');
    }
}
