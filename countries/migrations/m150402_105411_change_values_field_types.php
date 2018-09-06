<?php

use yii\db\Schema;
use yii\db\Migration;

class m150402_105411_change_values_field_types extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%company_value}}', 'value_va', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_oa', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_ia', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_kir', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_dkiz', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_kkiz', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_v', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_fr', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_fd', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_frn', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_pdn', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_chpzp', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_aosina', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_chdspood', Schema::TYPE_BIGINT);
        $this->alterColumn('{{%company_value}}', 'value_ebitda', Schema::TYPE_BIGINT);
    }

    public function down()
    {
        $this->alterColumn('{{%company_value}}', 'value_va', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_oa', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_ia', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_kir', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_dkiz', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_kkiz', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_v', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_fr', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_fd', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_frn', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_pdn', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_chpzp', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_aosina', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_chdspood', Schema::TYPE_INTEGER);
        $this->alterColumn('{{%company_value}}', 'value_ebitda', Schema::TYPE_INTEGER);
    }
}
