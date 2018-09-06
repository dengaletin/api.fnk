<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_093402_add_table_indexes extends Migration
{
    public function up()
    {
        $this->createIndex('idx_property_form_name', '{{%property_form}}', 'name');
        $this->createIndex('idx_mode_name', '{{%mode}}', 'name');
        $this->createIndex('idx_group_name', '{{%group}}', 'name');
        $this->createIndex('idx_currency_name', '{{%currency}}', 'name');
        $this->createIndex('idx_report_type_name', '{{%report_type}}', 'name');
    }

    public function down()
    {
        $this->dropIndex('idx_property_form_name', '{{%property_form}}');
        $this->dropIndex('idx_mode_name', '{{%mode}}');
        $this->dropIndex('idx_group_name', '{{%group}}');
        $this->dropIndex('idx_currency_name', '{{%currency}}');
        $this->dropIndex('idx_report_type_name', '{{%report_type}}');
    }
}
