<?php

use yii\db\Schema;
use yii\db\Migration;

class m150317_122432_add_company_ticker_index extends Migration
{
    public function up()
    {
        $this->createIndex('idx_company_ticker', '{{%company}}', 'ticker');
    }

    public function down()
    {
        $this->dropIndex('idx_company_ticker', '{{%company}}');
    }
}
