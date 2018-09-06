<?php

use yii\db\Schema;
use yii\db\Migration;

class m150513_105411_change_values_field_types extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%company_value}}', 'value_va', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_oa', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_ia', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_kir', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_dkiz', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_kkiz', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_v', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_fr', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_fd', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_frn', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_pdn', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_chpzp', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_aosina', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_chdspood', Schema::TYPE_DECIMAL . '(16,3)');
        $this->alterColumn('{{%company_value}}', 'value_ebitda', Schema::TYPE_DECIMAL . '(16,3)');
    }

    public function down()
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
}
